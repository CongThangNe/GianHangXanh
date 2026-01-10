(() => {
  const $ = (id) => document.getElementById(id);

  const toggleBtn = $('chatbot-toggle');
  const closeBtn = $('chatbot-close');
  const windowEl = $('chatbot-window');
  const messagesEl = $('chatbot-messages');
  const formEl = $('chatbot-form');
  const inputEl = $('chatbot-input');

  if (!toggleBtn || !closeBtn || !windowEl || !messagesEl || !formEl || !inputEl) {
    // If the widget isn't present, do nothing.
    return;
  }

  let greetedOnce = false;

  const normalize = (s) =>
    (s || '')
      .toString()
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/\s+/g, ' ')
      .trim();

  const faq = [
    { key: 'san pham', reply: 'Shop hiện đang kinh doanh các sản phẩm chất lượng cao' },
    { key: 'giam gia', reply: 'Hiện tại shop đang có nhiều ưu đãi hấp dẫn' },
    { key: 'gio mo cua', reply: 'Shop mở cửa từ 8h – 22h' },
    { key: 'doi tra', reply: 'Shop hỗ trợ đổi trả trong 7 ngày' },
  ];

  const addMessage = (who, text) => {
    const wrap = document.createElement('div');
    wrap.className = `chatbot-msg ${who}`;
    const bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.textContent = text;
    wrap.appendChild(bubble);
    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  };

  const botReply = (userText) => {
    const t = normalize(userText);

    // Greeting intents
    if (
      t.includes('xin chao') ||
      t === 'chao' ||
      t.startsWith('chao ') ||
      t.includes('hello') ||
      t === 'hi' ||
      t.startsWith('hi ')
    ) {
      return '👋 Xin chào! Mình là chatbot hỗ trợ. Bạn cần mình giúp gì không?';
    }

    for (const item of faq) {
      if (t.includes(item.key)) return item.reply;
    }

    return 'Mình chưa hiểu câu hỏi này 😅\nBạn có thể hỏi về: sản phẩm, giảm giá, giờ mở cửa, đổi trả.';
  };

  const openChat = () => {
    windowEl.classList.add('is-open');
    windowEl.setAttribute('aria-hidden', 'false');
    inputEl.focus();

    if (!greetedOnce) {
      greetedOnce = true;
      addMessage('bot', '👋 Xin chào! Mình là chatbot hỗ trợ. Bạn cần mình giúp gì không?');
    }
  };

  const closeChat = () => {
    windowEl.classList.remove('is-open');
    windowEl.setAttribute('aria-hidden', 'true');
  };

  toggleBtn.addEventListener('click', () => {
    if (windowEl.classList.contains('is-open')) closeChat();
    else openChat();
  });

  closeBtn.addEventListener('click', closeChat);

  formEl.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = (inputEl.value || '').trim();
    if (!text) return;

    addMessage('user', text);
    inputEl.value = '';

    // Small delay for a more natural feel
    const reply = botReply(text);
    window.setTimeout(() => addMessage('bot', reply), 250);
  });
})();
