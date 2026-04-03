import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('forumSearch', ({ initialQuery = '', action = '', suggestionsUrl = '' } = {}) => ({
    action,
    suggestionsUrl,
    open: false,
    loading: false,
    query: initialQuery,
    sections: [],
    activeIndex: -1,
    debounceTimer: null,
    requestId: 0,
    historyKey: 'forum-search-history',

    init() {
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

    get visibleSections() {
        if (this.query.trim() === '') {
            const historyItems = this.history.map((entry) => ({
                type: 'history',
                title: entry,
                subtitle: 'Recherche recente',
                query: entry,
                url: `${this.action}?search=${encodeURIComponent(entry)}`,
            }));

            const helperItems = [
                { type: 'query', title: 'user:moh', subtitle: 'Chercher un membre', query: 'user:moh' },
                { type: 'query', title: '#actualite', subtitle: 'Chercher un hashtag', query: '#actualite' },
                { type: 'query', title: 'category:sport', subtitle: 'Filtrer une categorie', query: 'category:sport' },
            ];

            return [
                ...(historyItems.length ? [{ label: 'Recent', items: historyItems }] : []),
                { label: 'Raccourcis', items: helperItems },
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

        if (this.query.trim() !== '' && this.sections.length === 0) {
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
                this.sections = [];
            }
        } finally {
            if (currentRequestId === this.requestId) {
                this.loading = false;
            }
        }
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

    onKeydown(event) {
        const items = this.flatItems;

        if (!this.open && ['ArrowDown', 'ArrowUp'].includes(event.key)) {
            this.open = true;
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (items.length === 0) {
                return;
            }
            this.activeIndex = (this.activeIndex + 1 + items.length) % items.length;
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (items.length === 0) {
                return;
            }
            this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
            return;
        }

        if (event.key === 'Escape') {
            this.close();
            return;
        }

        if (event.key === 'Enter' && this.open && this.activeIndex >= 0) {
            event.preventDefault();
            this.selectItem(items[this.activeIndex]);
        }
    },

    submitSearch() {
        const trimmedQuery = this.query.trim();

        if (trimmedQuery !== '') {
            this.storeHistory(trimmedQuery);
        }

        this.$refs.form?.submit();
    },

    selectItem(item) {
        if (!item) {
            return;
        }

        if (item.query) {
            this.query = item.query;
            this.storeHistory(item.query);
            this.close();
            this.$nextTick(() => this.$refs.form?.submit());
            return;
        }

        if (item.url) {
            if (item.title) {
                this.storeHistory(item.title);
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
