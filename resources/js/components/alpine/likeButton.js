export default (postId, initialLikesCount, isLiked) => ({
    postId: postId,
    likesCount: initialLikesCount,
    isLiked: isLiked,
    button: {
        ['x-on:click']() {
            this.like();
        },
        ['x-bind:title']() {
            return this.isLiked ? 'Liked' : 'Like this post';
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
                        title: 'Your like was successfully applied!',
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
