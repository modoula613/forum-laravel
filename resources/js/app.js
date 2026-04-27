import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const THEME_KEY = 'sphere-theme';

function resolveTheme() {
    try {
        return window.localStorage.getItem(THEME_KEY) === 'light' ? 'light' : 'dark';
    } catch {
        return 'dark';
    }
}

function applyTheme(mode) {
    document.documentElement.dataset.theme = mode === 'dark' ? 'dark' : 'light';
}

applyTheme(resolveTheme());

Alpine.store('theme', {
    mode: resolveTheme(),

    set(mode) {
        this.mode = mode === 'dark' ? 'dark' : 'light';
        applyTheme(this.mode);

        try {
            window.localStorage.setItem(THEME_KEY, this.mode);
        } catch {
            // Ignore storage errors and keep the current session theme.
        }
    },

    toggle() {
        this.set(this.mode === 'dark' ? 'light' : 'dark');
    },
});

Alpine.data('emojiComposer', ({
    initialValue = '',
    emojis = ['🙂', '😂', '🔥', '👏', '😍', '🤔', '😮', '🥲', '💬', '❤️'],
} = {}) => ({
    value: initialValue,
    emojis,

    insertEmoji(emoji) {
        const textarea = this.$refs.input;

        if (!textarea) {
            this.value = `${this.value}${emoji}`;
            return;
        }

        const start = textarea.selectionStart ?? this.value.length;
        const end = textarea.selectionEnd ?? this.value.length;
        const prefix = this.value.slice(0, start);
        const suffix = this.value.slice(end);
        const spacer = prefix && !prefix.endsWith(' ') ? ' ' : '';

        this.value = `${prefix}${spacer}${emoji} ${suffix}`.trimEnd();

        this.$nextTick(() => {
            const nextPosition = prefix.length + spacer.length + emoji.length + 1;
            textarea.focus();
            textarea.setSelectionRange(nextPosition, nextPosition);
        });
    },
}));

Alpine.data('forumSearch', ({
    initialQuery = '',
    action = '',
    suggestionsUrl = '',
    mockSections = null,
    historyKey = 'forum-search-history',
} = {}) => ({
    action,
    suggestionsUrl,
    mockSections,
    historyKey,
    open: false,
    loading: false,
    query: initialQuery,
    sections: [],
    activeIndex: -1,
    debounceTimer: null,
    requestId: 0,
    baseId: null,

    init() {
        this.baseId = `x-search-${Math.random().toString(36).slice(2, 9)}`;

        if (this.query.trim() !== '') {
            this.fetchSuggestions();
        }
    },

    get history() {
        try {
            return JSON.parse(window.localStorage.getItem(this.historyKey) || '[]');
        } catch {
            return [];
        }
    },

    get listboxId() {
        return `${this.baseId}-listbox`;
    },

    optionId(index) {
        return `${this.baseId}-option-${index}`;
    },

    get activeDescendant() {
        return this.activeIndex >= 0 ? this.optionId(this.activeIndex) : null;
    },

    get visibleSections() {
        if (this.query.trim() === '') {
            const historyItems = this.history.map((entry) => ({
                type: 'history',
                title: entry,
                subtitle: 'Recherche recente',
                query: entry,
                url: `${this.action}?search=${encodeURIComponent(entry)}`,
                historyValue: entry,
            }));

            return [
                ...(historyItems.length ? [{ label: 'Recherches recentes', items: historyItems }] : []),
                {
                    label: 'Exemples simples',
                    items: [
                        { type: 'query', title: 'Trouver un membre', subtitle: 'Exemple : Moh', query: 'user:moh', historyValue: 'moh' },
                        { type: 'query', title: 'Chercher un sujet d actualite', subtitle: 'Exemple : actualite', query: '#actualite', historyValue: 'actualite' },
                        { type: 'query', title: 'Ouvrir une categorie', subtitle: 'Exemple : sport', query: 'category:sport', historyValue: 'sport' },
                    ],
                },
            ];
        }

        return this.sections;
    },

    get flatItems() {
        return this.visibleSections.flatMap((section) => section.items);
    },

    onInput() {
        this.open = true;
        this.activeIndex = -1;

        window.clearTimeout(this.debounceTimer);
        this.debounceTimer = window.setTimeout(() => {
            this.fetchSuggestions();
        }, 180);
    },

    onFocus() {
        this.open = true;

        if (this.query.trim() === '' || this.sections.length === 0) {
            this.fetchSuggestions();
        }
    },

    async fetchSuggestions() {
        const trimmedQuery = this.query.trim();

        if (trimmedQuery === '') {
            this.sections = [];
            this.loading = false;
            return;
        }

        const currentRequestId = ++this.requestId;
        this.loading = true;

        try {
            if (!this.suggestionsUrl) {
                this.sections = this.buildMockSections(trimmedQuery);
                return;
            }

            const response = await fetch(`${this.suggestionsUrl}?query=${encodeURIComponent(trimmedQuery)}`, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Search suggestions request failed');
            }

            const payload = await response.json();

            if (currentRequestId !== this.requestId) {
                return;
            }

            this.sections = Array.isArray(payload.sections) ? payload.sections : [];
        } catch {
            if (currentRequestId === this.requestId) {
                this.sections = this.buildMockSections(trimmedQuery);
            }
        } finally {
            if (currentRequestId === this.requestId) {
                this.loading = false;
            }
        }
    },

    buildMockSections(query) {
        if (typeof this.mockSections === 'function') {
            return this.mockSections(query);
        }

        if (Array.isArray(this.mockSections) && this.mockSections.length > 0) {
            return this.mockSections;
        }

        return [
            {
                label: 'Recherche',
                items: [
                    {
                        type: 'search',
                        title: `Rechercher : ${query}`,
                        subtitle: 'Voir les sujets et reactions correspondants',
                        url: `${this.action}?search=${encodeURIComponent(query)}`,
                        historyValue: query,
                    },
                ],
            },
            {
                label: 'Raccourcis utiles',
                items: [
                    {
                        type: 'shortcut',
                        title: 'Chercher ce mot parmi les membres',
                        subtitle: 'Ouvrir directement la recherche des profils',
                        url: `/users?search=${encodeURIComponent(query)}`,
                        historyValue: `user:${query}`,
                    },
                    {
                        type: 'query',
                        title: 'Chercher un sujet avec ce mot-cle',
                        subtitle: 'Pratique pour retrouver un sujet ou un debat',
                        query: `#${query}`,
                        historyValue: query,
                    },
                    {
                        type: 'query',
                        title: 'Chercher une categorie avec ce nom',
                        subtitle: 'Utile si tu cherches un theme precis',
                        query: `category:${query}`,
                        historyValue: `category:${query}`,
                    },
                ],
            },
        ];
    },

    close() {
        this.open = false;
        this.activeIndex = -1;
    },

    clear() {
        this.query = '';
        this.sections = [];
        this.activeIndex = -1;
        this.open = true;
        this.$nextTick(() => this.$refs.input?.focus());
    },

    moveActive(delta) {
        const items = this.flatItems;

        if (items.length === 0) {
            return;
        }

        this.activeIndex = (this.activeIndex + delta + items.length) % items.length;

        this.$nextTick(() => {
            document.getElementById(this.optionId(this.activeIndex))?.scrollIntoView({
                block: 'nearest',
            });
        });
    },

    onKeydown(event) {
        if (!this.open && ['ArrowDown', 'ArrowUp'].includes(event.key)) {
            this.open = true;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.moveActive(1);
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.moveActive(-1);
            return;
        }

        if (event.key === 'Escape') {
            this.close();
            return;
        }

        if (event.key === 'Enter' && this.open && this.activeIndex >= 0) {
            event.preventDefault();
            this.selectItem(this.flatItems[this.activeIndex]);
        }
    },

    submitSearch() {
        const trimmedQuery = this.query.trim();

        if (trimmedQuery !== '') {
            this.storeHistory(trimmedQuery);
        }

        this.close();
        this.$refs.form?.submit();
    },

    selectItem(item) {
        if (!item) {
            return;
        }

        const historyValue = item.historyValue ?? item.query ?? item.title;

        if (item.query) {
            this.query = item.query;
            this.storeHistory(historyValue);
            this.close();
            this.$nextTick(() => this.$refs.form?.submit());
            return;
        }

        if (item.url) {
            if (historyValue) {
                this.storeHistory(historyValue);
            }

            window.location.href = item.url;
        }
    },

    storeHistory(value) {
        const trimmedValue = value.trim();

        if (trimmedValue === '') {
            return;
        }

        const nextHistory = [
            trimmedValue,
            ...this.history.filter((entry) => entry !== trimmedValue),
        ].slice(0, 6);

        window.localStorage.setItem(this.historyKey, JSON.stringify(nextHistory));
    },
}));

Alpine.start();
