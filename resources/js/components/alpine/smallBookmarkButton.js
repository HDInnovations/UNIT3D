export default (torrentId, bookmarked) => ({
    torrentId: torrentId,
    bookmarked: bookmarked,
    button: {
        ['x-on:click']() {
            this.bookmarked ? this.deleteBookmark() : this.createBookmark();
        },
        ['x-bind:title']() {
            return this.bookmarked ? 'Unbookmark' : 'Bookmark';
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
                    title: 'Torrent has been bookmarked successfully!',
                });
            })
            .catch((error) => {
                Swal.fire({
                    title: '<strong style="color: rgb(17,17,17);">Error</strong>',
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
                    title: 'Torrent has been unbookmarked successfully!',
                });
            })
            .catch((error) => {
                Swal.fire({
                    title: '<strong style="color: rgb(17,17,17);">Error</strong>',
                    icon: 'error',
                    html: error.response.data.message,
                    showCloseButton: true,
                });
            });
    },
});
