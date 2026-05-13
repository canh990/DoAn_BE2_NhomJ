<!-- ===== POST DETAIL MODAL ===== -->
<div id="post-detail-modal" class="hidden fixed inset-0 z-[100] bg-black/80 backdrop-blur-sm flex items-center justify-center p-0 sm:p-4 opacity-0 transition-opacity duration-300">
    <div class="relative w-full max-w-4xl h-full sm:h-auto sm:max-h-[90vh] flex flex-col scale-95 transition-transform duration-300">
        <!-- Close button -->
        <button onclick="window.closePostModal()" class="absolute -top-12 right-0 sm:-right-12 text-white/70 hover:text-white p-2 transition-all z-[110]">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>

        <!-- Content Area -->
        <div id="post-modal-content" class="bg-slate-900 sm:rounded-3xl overflow-y-auto custom-scrollbar flex-1 shadow-2xl border border-white/5">
            <div class="flex items-center justify-center p-20">
                <div class="flex flex-col items-center gap-4">
                    <span class="material-symbols-outlined text-5xl text-sky-400 animate-spin">refresh</span>
                    <p class="text-slate-400 font-medium">Đang tải bài viết...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.openPostModal = function(postId) {
        const modal = document.getElementById('post-detail-modal');
        const content = document.getElementById('post-modal-content');
        if (!modal || !content) return;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Show loading
        content.innerHTML = `
            <div class="flex items-center justify-center p-20">
                <div class="flex flex-col items-center gap-4">
                    <span class="material-symbols-outlined text-5xl text-sky-400 animate-spin">refresh</span>
                    <p class="text-slate-400 font-medium">Đang tải bài viết...</p>
                </div>
            </div>
        `;

        // Trigger animation
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.relative').classList.remove('scale-95');
        });

        // Fetch post content
        fetch(`/posts/${postId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Không thể tải bài viết');
            return res.text();
        })
        .then(html => {
            content.innerHTML = `<div class="p-4 sm:p-6">${html}</div>`;
            // Re-observe videos if any
            if (window.videoObserver) {
                content.querySelectorAll('video').forEach(v => {
                    window.videoObserver.observe(v);
                });
            }
        })
        .catch(err => {
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center p-20 text-center">
                    <span class="material-symbols-outlined text-5xl text-rose-500 mb-4">error</span>
                    <h3 class="text-xl font-bold text-white mb-2">Lỗi tải bài viết</h3>
                    <p class="text-slate-400 mb-6">${err.message}</p>
                    <button onclick="window.closePostModal()" class="px-6 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl transition-colors">Đóng</button>
                </div>
            `;
        });
    };

    window.closePostModal = function() {
        const modal = document.getElementById('post-detail-modal');
        if (!modal) return;

        modal.classList.add('opacity-0');
        modal.querySelector('.relative').classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('post-modal-content').innerHTML = '';
        }, 300);
    };

    // Close modal on background click
    document.getElementById('post-detail-modal')?.addEventListener('click', function(e) {
        if (e.target === this) window.closePostModal();
    });
</script>
