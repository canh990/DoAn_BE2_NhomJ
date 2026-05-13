<!-- ===== GLOBAL CONFIRM MODAL ===== -->
<div id="global-confirm-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="confirm-modal-content" class="glass-panel rounded-2xl w-full max-w-sm shadow-2xl scale-95 transition-transform duration-300 relative overflow-hidden">
        <div class="p-6 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-500/10 mb-4 border border-rose-500/20">
                <span class="material-symbols-outlined text-rose-500 text-3xl">warning</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2" id="confirm-modal-title">Xác nhận xóa</h3>
            <p class="text-sm text-slate-400 mb-6" id="confirm-modal-message">Bạn có chắc chắn muốn thực hiện hành động này không?</p>
            
            <div class="flex gap-3">
                <button type="button" onclick="window.closeConfirmModal()" class="flex-1 px-5 py-2.5 text-sm font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 rounded-xl transition-colors">Hủy</button>
                <button type="button" id="confirm-modal-submit" class="flex-1 px-5 py-2.5 text-sm font-semibold bg-rose-500 hover:bg-rose-600 text-white rounded-xl shadow-lg shadow-rose-500/20 transition-all active:scale-95">Xóa ngay</button>
            </div>
        </div>
    </div>
</div>
