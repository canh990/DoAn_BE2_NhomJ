<!-- ===== MODAL CHỈNH SỬA BÀI VIẾT ===== -->
<div id="edit-post-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="glass-panel rounded-2xl w-full max-w-lg shadow-2xl scale-95 transition-transform duration-300">
        <div class="flex items-center justify-between p-4 border-b border-sky-400/10">
            <h3 class="text-lg font-bold text-on-surface">Chỉnh sửa bài viết</h3>
            <button type="button" id="close-edit-modal" class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-full transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="edit-post-form" method="POST" class="p-4 flex flex-col gap-4">
            @csrf
            @method('PUT')
            <textarea id="edit-post-content" name="noi_dung" rows="5" class="w-full bg-slate-900/50 border border-sky-400/20 rounded-xl focus:ring-1 focus:ring-sky-400 text-slate-100 placeholder-slate-500 resize-none p-3" required></textarea>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancel-edit-btn" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-white rounded-xl shadow-lg shadow-sky-500/20 transition-all active:scale-95">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
