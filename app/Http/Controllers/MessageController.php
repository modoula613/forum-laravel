<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewPrivateMessageNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $currentUser = auth()->user();

        $conversationItems = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($currentUser) {
                $query->where('sender_id', $currentUser->id)
                    ->orWhere('receiver_id', $currentUser->id);
            })
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($currentUser) {
                return $message->sender_id === $currentUser->id
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function ($messages) use ($currentUser) {
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->sender_id === $currentUser->id
                    ? $lastMessage->receiver
                    : $lastMessage->sender;

                return (object) [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $messages
                        ->where('receiver_id', $currentUser->id)
                        ->where('is_read', false)
                        ->count(),
                ];
            })
            ->filter(fn ($conversation) => $conversation->user !== null)
            ->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $conversations = new LengthAwarePaginator(
            $conversationItems->forPage($currentPage, $perPage)->values(),
            $conversationItems->count(),
            $perPage,
            $currentPage,
            ['path' => route('messages.index')]
        );

        return view('messages.index', compact('conversations'));
    }

    public function conversation(User $user): View
    {
        $search = request('search');

        Message::where('receiver_id', auth()->id())
            ->where('sender_id', $user->id)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })
            ->when($search, fn ($query) => $query->where('content', 'like', "%{$search}%"))
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();

        return view('messages.conversation', compact('messages', 'user'));
    }

    public function send(Request $request): RedirectResponse
    {
        abort_if($request->user()->is_banned, 403, 'Votre compte est suspendu.');

        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        abort_if((int) $validated['receiver_id'] === (int) auth()->id(), 403);

        $receiver = User::findOrFail($validated['receiver_id']);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
        ]);

        $receiver->notify(new NewPrivateMessageNotification(auth()->user()));

        return back()->with('success', 'Message envoye.');
    }

    public function destroy(Message $message): RedirectResponse
    {
        abort_unless(
            $message->sender_id === auth()->id() || $message->receiver_id === auth()->id(),
            403
        );

        $message->delete();

        return back()->with('success', 'Message supprime.');
    }
}
