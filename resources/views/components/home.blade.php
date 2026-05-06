@extends('layouts.app')

@section('title', 'Bảng tin')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="glass-panel rounded-2xl p-4 border border-emerald-400/20 bg-emerald-500/10 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <section class="glass-panel rounded-2xl p-4">
        <div class="flex gap-4">
            <img class="w-12 h-12 rounded-full border border-sky-400/20 shrink-0" alt="Avatar" src="{{ Auth::user()->anh_dai_dien ? asset('storage/' . Auth::user()->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}">
            <div class="w-full">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <textarea id="post-content" name="noi_dung" maxlength="280" class="w-full bg-transparent border border-white/10 focus:border-sky-400 focus:ring-0 text-on-surface placeholder-slate-500 resize-none text-lg leading-relaxed rounded-3xl p-4 min-h-[140px]" placeholder="Bạn đang nghĩ gì?" rows="4">{{ old('noi_dung') }}</textarea>
                    @error('noi_dung')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <input type="file" id="post-image" name="anh" accept="image/*" class="hidden">
                    <div id="image-preview" class="mt-3 hidden">
                        <img id="preview-img" class="max-w-full h-auto rounded-lg border border-white/10">
                        <button type="button" id="remove-image" class="mt-2 text-red-400 hover:text-red-300 text-sm">Xóa ảnh</button>
                    </div>
                    <div class="h-px bg-white/5 my-3"></div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" id="image-btn" class="p-2 text-sky-300 hover:bg-sky-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="image">image</span>
                            </button>
                            <button type="button" class="p-2 text-tertiary hover:bg-tertiary/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="gif_box">gif_box</span>
                            </button>
                            <button type="button" class="p-2 text-green-400 hover:bg-green-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="label">label</span>
                            </button>
                            <button type="button" class="p-2 text-yellow-400 hover:bg-yellow-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="mood">mood</span>
                            </button>
                            <button type="button" class="p-2 text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                            </button>
                            <button type="button" class="p-2 text-primary hover:bg-sky-400/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined" data-icon="poll">poll</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span id="post-char-count" class="text-sm text-slate-400">0/280</span>
                            <button id="post-submit-button" type="submit" class="bg-primary/20 text-primary border border-primary/30 px-6 py-1.5 rounded-full font-semibold hover:bg-primary/30 transition-all disabled:cursor-not-allowed disabled:opacity-50">
                                Đăng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @forelse($posts as $post)
        <article class="glass-panel rounded-2xl overflow-hidden">
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img class="w-10 h-10 rounded-full border border-sky-400/20 object-cover" alt="{{ $post->user?->name ?? 'Người dùng' }}" src="{{ $post->user && $post->user->anh_dai_dien ? asset('storage/' . $post->user->anh_dai_dien) : asset('storage/avatars/avtmacdinh.png') }}">
                    <div>
                        <h3 class="font-bold text-sm text-on-surface">{{ $post->user?->name ?? 'Người dùng' }}</h3>
                        <p class="text-[10px] text-slate-400">{{ $post->created_at ? $post->created_at->diffForHumans() : 'Không xác định' }}</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-3">
                <p class="text-sm leading-relaxed text-on-surface-variant whitespace-pre-line">{{ $post->noi_dung }}</p>
                @if($post->media->count() > 0)
                    <div class="mt-3">
                        @foreach($post->media as $media)
                            @if($media->loai === 'hinh_anh')
                                <img src="{{ asset('storage/' . $media->duong_dan) }}" alt="Post image" class="w-full h-auto rounded-lg border border-white/10">
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center justify-around">
                    <button class="flex items-center gap-2 text-slate-400 hover:text-sky-300 transition-colors py-1.5 px-4 rounded-xl hover:bg-sky-400/10">
                        <span class="material-symbols-outlined" data-icon="thumb_up">thumb_up</span>
                        <span class="text-sm font-medium">Thích</span>
                    </button>
                    <button class="flex items-center gap-2 text-slate-400 hover:text-sky-300 transition-colors py-1.5 px-4 rounded-xl hover:bg-sky-400/10">
                        <span class="material-symbols-outlined" data-icon="chat_bubble">chat_bubble</span>
                        <span class="text-sm font-medium">Bình luận</span>
                    </button>
                    <button class="flex items-center gap-2 text-slate-400 hover:text-sky-300 transition-colors py-1.5 px-4 rounded-xl hover:bg-sky-400/10">
                        <span class="material-symbols-outlined" data-icon="share">share</span>
                        <span class="text-sm font-medium">Chia sẻ</span>
                    </button>
                </div>
            </div>
        </article>
    @empty
        <div class="glass-panel rounded-2xl p-6 text-center text-slate-300">
            <p class="text-sm">Chưa có bài viết nào. Hãy là người đầu tiên đăng trạng thái!</p>
        </div>
    @endforelse
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('post-content');
        const counter = document.getElementById('post-char-count');
        const submitButton = document.getElementById('post-submit-button');
        const imageBtn = document.getElementById('image-btn');
        const imageInput = document.getElementById('post-image');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const removeImageBtn = document.getElementById('remove-image');

        if (!textarea || !counter || !submitButton) {
            return;
        }

        const maxLength = Number(textarea.getAttribute('maxlength')) || 280;

        const updateCount = function () {
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength}`;
            updateSubmitButton();
        };

        const updateSubmitButton = function () {
            const hasText = textarea.value.trim().length > 0;
            const hasImage = imageInput.files && imageInput.files.length > 0;
            submitButton.disabled = !hasText && !hasImage;
        };

        textarea.addEventListener('input', updateCount);

        // Image upload handling
        imageBtn.addEventListener('click', function () {
            imageInput.click();
        });

        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
            updateSubmitButton();
        });

        removeImageBtn.addEventListener('click', function () {
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            updateSubmitButton();
        });

        updateCount();
    });
</script>
@endsection