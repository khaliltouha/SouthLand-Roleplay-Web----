document.addEventListener('DOMContentLoaded', () => {

    // --- DOM Element Selectors ---
    const header = document.querySelector('header');
    const navLinks = document.querySelector('.nav-links');
    const menuIcon = document.querySelector('.mobile-menu-toggle');

    // Discord Auth Elements
    const discordBtn = document.getElementById('discord-login');
    const userProfile = document.getElementById('user-profile');
    const userName = document.getElementById('user-name');
    const userAvatar = document.getElementById('user-avatar');
    const logoutBtn = document.getElementById('logout-btn');

    // Content Containers
    const rulesContainer = document.getElementById('rules-container');
    const teamContainer = document.getElementById('team-container');
    
    // Forms
    const contactForm = document.getElementById('contact-form');

    // Submissions Elements
    const submissionsContainer = document.getElementById('submissions-container');
    const submissionModal = document.getElementById('submission-modal');
    const modalBody = document.getElementById('modal-body');
    const modalCloseBtn = document.getElementById('modal-close-btn');

    let submissionsData = []; // To store submissions data globally

    // --- CONFIGURATION ---
    // !!! مهم: استبدل 'YOUR_CLIENT_ID' بالـ Client ID الخاص بتطبيقك من بوابة مطوري ديسكورد
    const CLIENT_ID = '1384155580658221207'; 
    const REDIRECT_URI = window.location.origin + window.location.pathname;
    const SCOPE = 'identify';

    // --- CORE FUNCTIONS ---
    const fetchData = async (url) => {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error(`Could not fetch data from ${url}:`, error);
            return null;
        }
    };

    // --- UI SETUP & ENHANCEMENTS ---
    const setupMobileMenu = () => {
        if (menuIcon && navLinks) {
            menuIcon.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    };

    const handleScroll = () => {
        if (header) {
            header.classList.toggle('scrolled', window.scrollY > 50);
        }
    };

    const setupScrollAnimations = () => {
        const sections = document.querySelectorAll('.site-section');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        sections.forEach(section => observer.observe(section));
    };

    // --- CONTENT RENDERING ---
    const renderRules = async () => {
        if (!rulesContainer) return;
        const rules = await fetchData('data/rules.json');
        if (!rules) return;
        
        rulesContainer.innerHTML = rules.map(rule => `
            <div class="accordion-item">
                <button class="accordion-header">${rule.title}</button>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <p>${rule.description}</p>
                    </div>
                </div>
            </div>
        `).join('');

        rulesContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('accordion-header')) {
                const activeItem = rulesContainer.querySelector('.accordion-item.active');
                const clickedItem = e.target.parentElement;

                if (activeItem && activeItem !== clickedItem) {
                    activeItem.classList.remove('active');
                    activeItem.querySelector('.accordion-content').style.maxHeight = null;
                }

                clickedItem.classList.toggle('active');
                const content = clickedItem.querySelector('.accordion-content');
                if (clickedItem.classList.contains('active')) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                } else {
                    content.style.maxHeight = null;
                }
            }
        });
    };

    const renderTeam = async () => {
        if (!teamContainer) return;
        const team = await fetchData('data/team.json');
        if (!team) return;
        
        teamContainer.innerHTML = team.map(member => `
            <div class="team-card">
                <img src="${member.image}" alt="${member.name}" class="team-card-img">
                <div class="team-card-info">
                    <h3>${member.name}</h3>
                    <p>${member.role}</p>
                </div>
            </div>
        `).join('');
    };

    // --- CONTACT FORM ---
    const setupContactForm = () => {
        if (!contactForm) return;

        const CONTACT_WEBHOOK_URL = 'https://discord.com/api/webhooks/1384054944104976445/YRNj9G4RiRVT4DfgTDcxSJx2kiW17rYYvU6hJih1wlamsOK4GcUlwMp1RkjoM4IynPPb';

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (CONTACT_WEBHOOK_URL.includes('#ضع_رابط')) {
                alert('يرجى تعيين رابط ويبهوك التواصل في ملف script.js أولاً!');
                return;
            }

            const submitButton = contactForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'جارٍ الإرسال...';

            const formData = new FormData(contactForm);
            const name = formData.get('name');
            const email = formData.get('email');
            const message = formData.get('message');

            const embed = {
                title: 'رسالة تواصل جديدة',
                color: 15844367, // Gold color
                fields: [
                    { name: 'الاسم', value: name, inline: true },
                    { name: 'البريد الإلكتروني', value: email, inline: true },
                    { name: 'الرسالة', value: message, inline: false }
                ],
                footer: {
                    text: `Future Life Network | ${new Date().toLocaleString()}`
                }
            };

            try {
                const response = await fetch(CONTACT_WEBHOOK_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ embeds: [embed] })
                });

                if (!response.ok) {
                    throw new Error(`Webhook error: ${response.statusText}`);
                }

                alert('شكراً لتواصلك! تم استلام رسالتك بنجاح.');
                contactForm.reset();

            } catch (error) {
                console.error('Failed to send contact message:', error);
                alert('عذراً، حدث خطأ أثناء إرسال رسالتك. يرجى المحاولة مرة أخرى.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });
    };

    // --- DISCORD AUTHENTICATION ---
    const handleLogin = () => {
        if (CLIENT_ID === 'YOUR_CLIENT_ID') {
            alert('يرجى تعيين CLIENT_ID في ملف script.js أولاً!');
            return;
        }
        const authUrl = `https://discord.com/api/oauth2/authorize?client_id=${CLIENT_ID}&redirect_uri=${encodeURIComponent(REDIRECT_URI)}&response_type=token&scope=${SCOPE}`;
        window.location.href = authUrl;
    };

    const handleLogout = () => {
        localStorage.removeItem('discord_token');
        localStorage.removeItem('discord_token_type');
        updateUIWithUser(null);
    };

    const getFragmentParams = () => {
        const fragment = new URLSearchParams(window.location.hash.slice(1));
        const accessToken = fragment.get('access_token');
        const tokenType = fragment.get('token_type');
        
        if (accessToken && tokenType) {
            localStorage.setItem('discord_token', accessToken);
            localStorage.setItem('discord_token_type', tokenType);
            window.history.replaceState({}, document.title, window.location.pathname + window.location.search);
        }
    };

    const fetchDiscordUser = async (token, tokenType) => {
        try {
            const response = await fetch('https://discord.com/api/users/@me', {
                headers: { authorization: `${tokenType} ${token}` },
            });
            if (!response.ok) {
                if (response.status === 401) handleLogout();
                throw new Error(`Error fetching user: ${response.statusText}`);
            }
            return await response.json();
        } catch (error) {
            console.error(error);
            return null;
        }
    };

    const updateUIWithUser = (user) => {
        const isLoggedIn = !!user;

        if (isLoggedIn) {
            discordBtn.classList.add('hidden');
            userProfile.classList.remove('hidden');
            userName.textContent = user.username;
            userAvatar.src = user.avatar 
                ? `https://cdn.discordapp.com/avatars/${user.id}/${user.avatar}.png` 
                : `https://cdn.discordapp.com/embed/avatars/${user.discriminator % 5}.png`;
        } else {
            discordBtn.classList.remove('hidden');
            userProfile.classList.add('hidden');
            userName.textContent = '';
            userAvatar.src = '';
        }

        // Re-render submission cards to reflect login state
        renderSubmissions(submissionsData, isLoggedIn);
    };

    const initAuth = async () => {
        getFragmentParams();
        const token = localStorage.getItem('discord_token');
        const tokenType = localStorage.getItem('discord_token_type');

        if (token && tokenType) {
            const user = await fetchDiscordUser(token, tokenType);
            updateUIWithUser(user);
        }

        discordBtn.addEventListener('click', handleLogin);
        logoutBtn.addEventListener('click', handleLogout);
    };

    // --- SUBMISSIONS LOGIC ---

    const fetchSubmissions = async () => {
        try {
            const response = await fetch('data/submissions.json');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error("Could not fetch submissions:", error);
            return [];
        }
    };

    const renderSubmissions = (submissions, isLoggedIn) => {
        if (!submissionsContainer) return;
        submissionsContainer.innerHTML = submissions.map(sub => {
            const isSubmissionOpen = sub.status === 'open';
            let buttonText = '';
            let buttonDisabled = true;
            let buttonClass = 'disabled';

            if (!isLoggedIn && isSubmissionOpen) {
                buttonText = 'يجب عليك تسجيل الدخول اولا';
            } else if (isLoggedIn && isSubmissionOpen) {
                buttonText = 'قدم الآن';
                buttonDisabled = false;
                buttonClass = '';
            } else {
                buttonText = 'التقديم مغلق';
            }

            return `
                <div class="submission-card">
                    <img src="${sub.image}" alt="${sub.title}" class="submission-card-img">
                    <div class="submission-card-content">
                        <h3>${sub.title}</h3>
                        <button 
                            class="btn btn-primary ${buttonClass}" 
                            data-submission-id="${sub.id}" 
                            ${buttonDisabled ? 'disabled' : ''}>
                            ${buttonText}
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    };

    const generateFormHTML = (submission) => {
        const questionsHTML = submission.questions.map(q => {
            let inputHTML = '';
            switch (q.type) {
                case 'text':
                case 'number':
                    inputHTML = `<input type="${q.type}" id="${q.id}" name="${q.id}" required ${q.placeholder ? `placeholder="${q.placeholder}"` : ''}>`;
                    break;
                case 'textarea':
                    inputHTML = `<textarea id="${q.id}" name="${q.id}" required></textarea>`;
                    break;
                case 'boolean':
                    inputHTML = `
                        <div class="radio-group">
                            <label><input type="radio" name="${q.id}" value="نعم" required> نعم</label>
                            <label><input type="radio" name="${q.id}" value="لا"> لا</label>
                        </div>`;
                    break;
                case 'select':
                    const optionsHTML = q.options.map(opt => `<option value="${opt}">${opt}</option>`).join('');
                    inputHTML = `<select id="${q.id}" name="${q.id}" required>${optionsHTML}</select>`;
                    break;
            }
            return `
                <div class="form-group">
                    <label for="${q.id}">${q.text}</label>
                    ${inputHTML}
                </div>
            `;
        }).join('');

        return `
            <h3>${submission.title}</h3>
            <form class="submission-form" data-webhook="${submission.webhook}">
                ${questionsHTML}
                <button type="submit" class="btn btn-primary">إرسال التقديم</button>
            </form>
        `;
    };

    const handleSubmissionClick = (e) => {
        if (!e.target.matches('.btn[data-submission-id]')) return;
        
        const button = e.target;
        if (button.classList.contains('disabled')) return;

        const submissionId = button.dataset.submissionId;
        const submission = submissionsData.find(s => s.id === submissionId);

        if (submission) {
            modalBody.innerHTML = generateFormHTML(submission);
            submissionModal.classList.remove('hidden');
        }
    };

    const handleFormSubmit = async (e) => {
        if (!e.target.matches('.submission-form')) return;

        e.preventDefault();
        const form = e.target;
        const webhookUrl = form.dataset.webhook;
        const formData = new FormData(form);
        const user = await fetchDiscordUser(localStorage.getItem('discord_token'), localStorage.getItem('discord_token_type'));

        if (!user) {
            alert('حدث خطأ في جلب معلومات المستخدم. يرجى المحاولة مرة أخرى.');
            return;
        }

        const fields = [];
        for (let [name, value] of formData.entries()) {
            const question = submissionsData.flatMap(s => s.questions).find(q => q.id === name);
            if (question) {
                fields.push({ name: question.text, value: value, inline: false });
            }
        }

        const embed = {
            title: `تقديم جديد: ${modalBody.querySelector('h3').textContent}`,
            description: `تقديم جديد من <@${user.id}>.`,
            color: 3092790, // A nice blue color
            fields: fields,
            footer: {
                text: `Future Life Network | ${new Date().toLocaleString()}`
            }
        };

        try {
            const response = await fetch(webhookUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ embeds: [embed] })
            });
            if (!response.ok) throw new Error(`Webhook error: ${response.statusText}`);
            alert('تم إرسال تقديمك بنجاح!');
            submissionModal.classList.add('hidden');
        } catch (error) {
            console.error('Failed to send submission:', error);
            alert('حدث خطأ أثناء إرسال التقديم. يرجى المحاولة مرة أخرى.');
        }
    };

    const initSubmissions = async () => {
        submissionsData = await fetchSubmissions();
        const isLoggedIn = !!localStorage.getItem('discord_token');
        renderSubmissions(submissionsData, isLoggedIn);

        if (submissionsContainer) {
            submissionsContainer.addEventListener('click', handleSubmissionClick);
        }

        if (submissionModal) {
            submissionModal.addEventListener('click', (e) => {
                if (e.target === submissionModal || e.target === modalCloseBtn) {
                    submissionModal.classList.add('hidden');
                }
            });
            modalBody.addEventListener('submit', handleFormSubmit);
        }
    };

    // --- INITIALIZATION ---
    const init = () => {
        setupMobileMenu();
        setupScrollAnimations();
        setupContactForm();
        renderRules();
        renderTeam();
        initAuth();
        initSubmissions();
        window.addEventListener('scroll', handleScroll);
    };

    init();
});
