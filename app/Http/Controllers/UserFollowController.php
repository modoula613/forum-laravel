<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    public function toggle(Request $request, User $user): RedirectResponse
    {
        abort_if(auth()->id() === $user->id, 403);

        $currentUser = auth()->user();
        $action = $request->string('action')->toString() ?: 'request';

        if ($action === 'unfollow' && $currentUser->isFollowing($user)) {
            $currentUser->followingUsers()->detach($user->id);

            return back()->with('success', 'Suivi retire.');
        }

        if ($action === 'cancel_request' && $currentUser->hasPendingFollowRequestTo($user)) {
            $currentUser->sentFollowRequests()->detach($user->id);

            return back()->with('success', 'Demande de suivi annulee.');
        }

        if ($action === 'decline_request' && $currentUser->hasPendingFollowRequestFrom($user)) {
            $currentUser->receivedFollowRequests()->detach($user->id);

            return back()->with('success', 'Demande de suivi refusee.');
        }

        if ($action === 'accept_request' && $currentUser->hasPendingFollowRequestFrom($user)) {
            $currentUser->receivedFollowRequests()->detach($user->id);
            $user->followingUsers()->syncWithoutDetaching([$currentUser->id]);

            $message = $currentUser->isFollowing($user)
                ? 'Demande acceptee. Vous etes maintenant amis.'
                : 'Demande acceptee.';

            return back()->with('success', $message);
        }

        if ($currentUser->isFollowing($user)) {
            return back()->with('success', 'Tu suis deja ce membre.');
        }

        if ($currentUser->hasPendingFollowRequestTo($user)) {
            return back()->with('success', 'La demande de suivi est deja en attente.');
        }

        $currentUser->sentFollowRequests()->syncWithoutDetaching([$user->id]);

        return back()->with('success', 'Demande de suivi envoyee.');
    }
}
