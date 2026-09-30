document.addEventListener('alpine:init', () => {
    Alpine.data('likeButton', (postId, initialLikesCount, isLiked) => ({
        postId: postId,
        likesCount: initialLikesCount,
        isLiked: isLiked,
        button: {
            ['x-on:click']() {
                this.like();
            },
            ['x-bind:title']() {
                return this.isLiked ? window.i18n.liked : window.i18n.likeThisPost;
            },
        },
        icon: {
            ['x-bind:class']() {
                return this.isLiked && 'post__like-animation';
            },
        },
        like() {
            axios
                .post(`/api/posts/${this.postId}/like`)
                .then((response) => {
                    const data = response.data;
                    if (data.success) {
                        this.likesCount++;
                        this.isLiked = true;
                        Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                        }).fire({
                            icon: 'success',
                            title: window.i18n.likeApplied,
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
