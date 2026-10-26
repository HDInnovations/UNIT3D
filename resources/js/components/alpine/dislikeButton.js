export default (postId, initialDislikesCount, isDisliked) => ({
    postId: postId,
    dislikesCount: initialDislikesCount,
    isDisliked: isDisliked,
    button: {
        ['x-on:click']() {
            this.dislike();
        },
        ['x-bind:title']() {
            return this.isDisliked ? 'Disliked' : 'Dislike this post';
        },
    },
    icon: {
        ['x-bind:class']() {
            return this.isDisliked && 'post__like-animation';
        },
    },
    dislike() {
        axios
            .post(`/api/posts/${this.postId}/dislike`)
            .then((response) => {
                const data = response.data;
                if (data.success) {
                    this.dislikesCount++;
                    this.isDisliked = true;
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    }).fire({
                        icon: 'success',
                        title: 'Your dislike was successfully applied!',
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    title: '<strong style="color: rgb(17,17,17);">Error</strong>',
                    icon: 'error',
                    html: error.response.data.message || error.message,
                    showCloseButton: true,
                });
            });
    },
});
