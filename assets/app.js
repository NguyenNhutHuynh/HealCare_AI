document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const menu = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');
    const fontButton = document.querySelector('[data-font-toggle]');

    menu?.addEventListener('click', () => nav?.classList.toggle('open'));
    fontButton?.addEventListener('click', () => {
        body.classList.toggle('large-text');
        localStorage.setItem('healcare-large-text', body.classList.contains('large-text') ? '1' : '0');
    });
    if (localStorage.getItem('healcare-large-text') === '1') body.classList.add('large-text');

    const form = document.querySelector('[data-chat-form]');
    const messages = document.querySelector('[data-messages]');
    const status = document.querySelector('[data-chat-status]');
    const historyKey = 'healcare-chat-history';
    let history = JSON.parse(localStorage.getItem(historyKey) || '[]');

    const addMessage = (role, text) => {
        const item = document.createElement('div');
        item.className = `message ${role}`;
        item.innerHTML = `<span class="message-label">${role === 'user' ? 'Bạn' : 'Healcare AI'}</span><p></p>`;
        item.querySelector('p').textContent = text;
        messages?.appendChild(item);
        messages?.scrollTo({ top: messages.scrollHeight, behavior: 'smooth' });
    };

    const send = async (question) => {
        if (!question || !form) return;
        addMessage('user', question);
        const submit = form.querySelector('button');
        if (submit) submit.disabled = true;
        if (status) status.textContent = 'Healcare đang suy nghĩ...';
        try {
            const response = await fetch('index.php?page=chat', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: question, history })
            });
            const data = await response.json();
            addMessage('assistant', data.message || 'Xin lỗi, hệ thống chưa trả lời được.');
            history.push({ role: 'user', content: question }, { role: 'assistant', content: data.message || '' });
            localStorage.setItem(historyKey, JSON.stringify(history.slice(-12)));
            if (status) status.textContent = data.mode === 'fallback' ? 'Đang dùng hướng dẫn nội bộ. Cấu hình API để nhận câu trả lời AI thật.' : '';
        } catch (error) {
            addMessage('assistant', 'Không thể kết nối lúc này. Bạn hãy thử lại sau.');
            if (status) status.textContent = 'Lỗi kết nối.';
        } finally {
            if (submit) submit.disabled = false;
        }
    };

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        const input = form.querySelector('textarea');
        const question = input?.value.trim();
        if (question) { input.value = ''; send(question); }
    });
    document.querySelectorAll('[data-suggest]').forEach((button) => button.addEventListener('click', () => send(button.dataset.suggest)));
    document.querySelector('[data-clear-chat]')?.addEventListener('click', () => {
        history = [];
        localStorage.removeItem(historyKey);
        if (messages) messages.innerHTML = '<div class="message assistant"><span class="message-label">Healcare AI</span><p>Đoạn chat đã được xóa. Bạn muốn hỏi điều gì?</p></div>';
    });

    const planButton = document.querySelector('[data-generate-plan]');
    const planBox = document.querySelector('[data-ai-plan]');
    const planStatus = document.querySelector('[data-plan-status]');
    planButton?.addEventListener('click', async () => {
        planButton.disabled = true;
        if (planStatus) planStatus.textContent = 'AI đang lập thực đơn...';
        try {
            const response = await fetch('index.php?page=recommendations', { method: 'POST' });
            const data = await response.json();
            if (planBox) {
                planBox.hidden = false;
                planBox.textContent = data.message || 'Chưa có đề xuất.';
            }
            if (planStatus) planStatus.textContent = data.mode === 'fallback' ? 'Đang dùng chế độ dự phòng.' : 'Đề xuất đã được tạo.';
        } catch (error) {
            if (planStatus) planStatus.textContent = 'Không thể kết nối AI lúc này.';
        } finally {
            planButton.disabled = false;
        }
    });
});
