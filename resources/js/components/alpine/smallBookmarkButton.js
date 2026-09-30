document.addEventListener('alpine:init', () => {
    Alpine.data('bookmark', (torrentId, bookmarked) => ({
        torrentId: torrentId,
        bookmarked: bookmarked,
        button: {
            ['x-on:click']() {
                this.bookmarked ? this.deleteBookmark() : this.createBookmark();
            },
            ['x-bind:title']() {
                return this.bookmarked ? window.i18n.unbookmark : window.i18n.bookmark;
            },
        },
        icon: {
            ['x-bind:class']() {
                return this.bookmarked ? 'fa-bookmark-slash' : 'fa-bookmark';
            },
        },
        createBookmark() {
            axios
                .post(`/api/bookmarks/${this.torrentId}`)
                .then((response) => {
                    this.bookmarked = Boolean(response.data);
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: 'success',
                        title: window.i18n.bookmarkApplied,
                    });
                })
                .catch((error) => {
                    Swal.fire({
                        title: `<strong style="color: rgb(17,17,17);">${window.i18n.errorTitle}</strong>`,
                        icon: 'error',
                        html: error.response.data.message,
                        showCloseButton: true,
                    });
                });
        },
        deleteBookmark() {
            axios
                .delete(`/api/bookmarks/${this.torrentId}`)
                .then((response) => {
                    this.bookmarked = Boolean(response.data);
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: 'success',
                        title: window.i18n.unbookmarkApplied,
                    });
                })
                .catch((error) => {
                    Swal.fire({
                        title: `<strong style="color: rgb(17,17,17);">${window.i18n.errorTitle}</strong>`,
                        icon: 'error',
                        html: error.response.data.message,
                        showCloseButton: true,
                    });
                });
        },
    }));
});
