document.addEventListener('alpine:init', () => {
    Alpine.data('dislikeButton', (postId, initialDislikesCount, isDisliked) => ({
        postId: postId,
        dislikesCount: initialDislikesCount,
        isDisliked: isDisliked,
        button: {
            ['x-on:click']() {
                this.dislike();
            },
            ['x-bind:title']() {
                return this.isDisliked ? window.i18n.disliked : window.i18n.dislikeThisPost;
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
                            title: window.i18n.dislikeApplied,
                        });
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        title: `<strong style="color: rgb(17,17,17);">${window.i18n.errorTitle}</strong>`,
                        icon: 'error',
                        html: error.response.data.message || error.message,
                        showCloseButton: true,
                    });
                });
        },
    }));
});
