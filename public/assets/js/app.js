document.addEventListener('DOMContentLoaded', function () {
    initImageFallbacks();
    initAboutSlider();
    initHomePrograms();
    initTestimonialSlider();
    initNewsPage();
    initArticleShare();
    initAboutPage();
    initGlobalReveal();
});

/* ============================================================
   Image Fallback
   ============================================================ */

function initImageFallbacks() {
    const images = Array.from(document.querySelectorAll('img[data-fallback]'));

    images.forEach((image) => {
        image.addEventListener('error', function () {
            const fallback = this.dataset.fallback;

            if (!fallback || this.src === fallback) return;

            this.src = fallback;
        });
    });
}

/* ============================================================
   About Slider
   ============================================================ */

function initAboutSlider() {
    const track = document.querySelector('.sb-about-track');
    const dotsWrapper = document.querySelector('[data-slider="about"]');

    if (!track || !dotsWrapper) return;

    const slides = Array.from(track.querySelectorAll('img'));
    if (!slides.length) return;

    let currentIndex = 0;
    let autoSlide = null;

    dotsWrapper.innerHTML = '';

    slides.forEach((_, index) => {
        const dot = document.createElement('button');

        dot.type = 'button';
        dot.setAttribute('aria-label', `Tampilkan foto ${index + 1}`);
        dot.classList.toggle('active', index === 0);

        dot.addEventListener('click', () => {
            goToSlide(index);
            restartAutoSlide();
        });

        dotsWrapper.appendChild(dot);
    });

    const dots = Array.from(dotsWrapper.querySelectorAll('button'));

    function goToSlide(index) {
        currentIndex = (index + slides.length) % slides.length;
        track.style.transform = `translateX(-${currentIndex * 100}%)`;

        dots.forEach((dot, i) => {
            const isActive = i === currentIndex;

            dot.classList.toggle('active', isActive);
            dot.setAttribute('aria-current', isActive ? 'true' : 'false');
        });
    }

    function startAutoSlide() {
        if (slides.length <= 1) return;

        autoSlide = window.setInterval(() => {
            goToSlide(currentIndex + 1);
        }, 4600);
    }

    function restartAutoSlide() {
        if (autoSlide) window.clearInterval(autoSlide);
        startAutoSlide();
    }

    goToSlide(0);
    startAutoSlide();
}

/* ============================================================
   Home Programs Slider
   ============================================================ */

function initHomePrograms() {
    const slider = document.getElementById('homeProgramsSlider');

    if (!slider) return;

    const cards = Array.from(slider.querySelectorAll('[data-program-card]'));

    if (!cards.length) return;

    cards.forEach((card) => {
        const image = card.dataset.programImage;
        const bg = card.querySelector('.sb-program-card-bg');

        if (!image || !bg) return;

        card.style.setProperty('--program-image', `url("${image}")`);
        bg.style.backgroundImage = `url("${image}")`;
    });

    let activeIndex = cards.findIndex((card) => card.classList.contains('active'));

    if (activeIndex < 0) {
        activeIndex = 0;
    }

    function setActiveCard(index) {
        activeIndex = (index + cards.length) % cards.length;

        cards.forEach((card, cardIndex) => {
            const isActive = cardIndex === activeIndex;
            const trigger = card.querySelector('[data-program-trigger]');
            const image = card.dataset.programImage;
            const bg = card.querySelector('.sb-program-card-bg');

            if (image && bg) {
                card.style.setProperty('--program-image', `url("${image}")`);
                bg.style.backgroundImage = `url("${image}")`;
            }

            card.classList.toggle('active', isActive);
            card.setAttribute('aria-expanded', isActive ? 'true' : 'false');

            if (trigger) {
                trigger.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                trigger.setAttribute(
                    'aria-label',
                    isActive
                        ? `Program aktif: ${trigger.textContent.trim()}`
                        : 'Klik untuk membuka program ini'
                );
            }
        });
    }

    cards.forEach((card, index) => {
        const trigger = card.querySelector('[data-program-trigger]');

        if (!trigger) return;

        trigger.addEventListener('click', function () {
            if (index === activeIndex) return;

            setActiveCard(index);
        });

        trigger.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;

            event.preventDefault();

            if (index !== activeIndex) {
                setActiveCard(index);
            }
        });
    });

    setActiveCard(activeIndex);
}

/* ============================================================
   Testimonial Slider
   Sekarang dynamic: teks diambil dari data-testimonial / isi card.
   ============================================================ */

function initTestimonialSlider() {
    const prevBtn = document.getElementById('testimonialPrev');
    const nextBtn = document.getElementById('testimonialNext');
    const textEl = document.getElementById('testimonialText');
    const cards = Array.from(document.querySelectorAll('.sb-person-card'));

    if (!prevBtn || !nextBtn || !textEl || !cards.length) return;

    const testimonials = cards.map((card) => {
        const datasetText = card.dataset.testimonial;
        const hiddenText = card.querySelector('[data-testimonial-text]');
        const quoteText = card.querySelector('blockquote');
        const paragraphText = card.querySelector('p');

        return (
            datasetText ||
            hiddenText?.textContent ||
            quoteText?.textContent ||
            paragraphText?.textContent ||
            textEl.textContent ||
            ''
        ).trim();
    });

    let currentIndex = cards.findIndex((card) => {
        return card.classList.contains('active') || card.classList.contains('is-center');
    });

    if (currentIndex < 0) {
        currentIndex = Math.min(2, cards.length - 1);
    }

    let isAnimating = false;

    function clearCardClasses(card) {
        card.classList.remove(
            'is-center',
            'is-left-1',
            'is-left-2',
            'is-right-1',
            'is-right-2',
            'is-hidden',
            'active'
        );
    }

    function getRelativePosition(index) {
        const total = cards.length;
        let diff = index - currentIndex;

        if (diff > total / 2) diff -= total;
        if (diff < -total / 2) diff += total;

        return diff;
    }

    function updateCards() {
        cards.forEach((card, index) => {
            clearCardClasses(card);

            const position = getRelativePosition(index);

            if (position === 0) {
                card.classList.add('is-center', 'active');
            } else if (position === -1) {
                card.classList.add('is-left-1');
            } else if (position === -2) {
                card.classList.add('is-left-2');
            } else if (position === 1) {
                card.classList.add('is-right-1');
            } else if (position === 2) {
                card.classList.add('is-right-2');
            } else {
                card.classList.add('is-hidden');
            }
        });
    }

    function updateText(index) {
        const text = testimonials[index] || '';

        if (!text) return;

        textEl.classList.add('is-changing');

        window.setTimeout(() => {
            textEl.textContent = text;
            textEl.classList.remove('is-changing');
        }, 120);
    }

    function goToSlide(index) {
        if (isAnimating || !cards.length) return;

        isAnimating = true;
        currentIndex = (index + cards.length) % cards.length;

        updateCards();
        updateText(currentIndex);

        window.setTimeout(() => {
            isAnimating = false;
        }, 360);
    }

    nextBtn.addEventListener('click', () => {
        goToSlide(currentIndex + 1);
    });

    prevBtn.addEventListener('click', () => {
        goToSlide(currentIndex - 1);
    });

    cards.forEach((card, index) => {
        card.addEventListener('click', () => {
            goToSlide(index);
        });

        card.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;

            event.preventDefault();
            goToSlide(index);
        });
    });

    updateCards();
    updateText(currentIndex);
}

/* ============================================================
   News Page
   ============================================================ */

function initNewsPage() {
    initNewsRevealAnimation();
    initNewsListing();
    initNewsEmailForm();
}

function initNewsRevealAnimation() {
    const page = document.querySelector('.sb-news-page');

    if (!page) return;

    const revealTargets = Array.from(page.querySelectorAll(
        '.sb-news-hero-centered, .sb-news-hero-copy, .sb-news-hero-visual, .sb-featured-news, .sb-news-toolbar, .sb-news-side-card, .sb-newsletter-card'
    ));

    if (!revealTargets.length) return;

    revealTargets.forEach((element, index) => {
        element.classList.add('sb-news-animate');
        element.style.transitionDelay = `${Math.min(index * 70, 280)}ms`;
    });

    if (!('IntersectionObserver' in window)) {
        revealTargets.forEach((element) => {
            element.classList.add('is-visible');
        });

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -8% 0px',
    });

    revealTargets.forEach((element) => {
        observer.observe(element);
    });
}

function initNewsListing() {
    const cards = Array.from(document.querySelectorAll('.sb-news-item-card'));
    const searchInput = document.getElementById('newsSearchInput');
    const searchButton = document.querySelector('[data-news-search-submit]');
    const categorySelect = document.getElementById('newsCategorySelect');
    const emptyState = document.getElementById('newsEmptyState');
    const pagination = document.getElementById('newsPagination');
    const newsGrid = document.getElementById('newsListGrid');

    if (!cards.length || !pagination) return;

    const hasServerPagination = Boolean(pagination.querySelector('a[href]'));
    const forceClientPagination = pagination.dataset.clientPagination === 'true';
    const useClientPagination = forceClientPagination || !hasServerPagination;

    const itemsPerPage = Number(pagination.dataset.perPage || 4);

    let activeFilter = categorySelect?.value || 'all';
    let currentPage = 1;
    let searchTimer = null;

    function normalize(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .trim();
    }

    function getMatchedCards() {
        const keyword = normalize(searchInput ? searchInput.value : '');
        activeFilter = categorySelect?.value || 'all';

        return cards.filter((card) => {
            const category = normalize(card.dataset.category || 'all');
            const title = normalize(card.dataset.title || '');
            const content = normalize(card.dataset.content || card.textContent);
            const searchableText = `${title} ${content}`;
            const selectedCategory = normalize(activeFilter);

            const matchCategory =
                selectedCategory === 'all' || category === selectedCategory;

            const matchKeyword =
                !keyword || searchableText.includes(keyword);

            return matchCategory && matchKeyword;
        });
    }

    function renderPagination(totalPages) {
        if (!useClientPagination) return;

        pagination.innerHTML = '';

        if (totalPages <= 1) {
            pagination.hidden = true;
            return;
        }

        pagination.hidden = false;

        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.setAttribute('aria-label', 'Halaman sebelumnya');
        prevBtn.disabled = currentPage === 1;
        prevBtn.innerHTML = `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M15 6L9 12L15 18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `;

        prevBtn.addEventListener('click', () => {
            if (currentPage <= 1) return;

            currentPage -= 1;
            updateNewsView(true);
        });

        pagination.appendChild(prevBtn);

        for (let page = 1; page <= totalPages; page += 1) {
            const pageBtn = document.createElement('button');

            pageBtn.type = 'button';
            pageBtn.textContent = page;
            pageBtn.classList.toggle('active', page === currentPage);
            pageBtn.setAttribute('aria-label', `Halaman ${page}`);

            if (page === currentPage) {
                pageBtn.setAttribute('aria-current', 'page');
            }

            pageBtn.addEventListener('click', () => {
                if (currentPage === page) return;

                currentPage = page;
                updateNewsView(true);
            });

            pagination.appendChild(pageBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.setAttribute('aria-label', 'Halaman berikutnya');
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.innerHTML = `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M9 6L15 12L9 18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `;

        nextBtn.addEventListener('click', () => {
            if (currentPage >= totalPages) return;

            currentPage += 1;
            updateNewsView(true);
        });

        pagination.appendChild(nextBtn);
    }

    function scrollToNewsList() {
        if (!newsGrid) return;

        const targetY = window.scrollY + newsGrid.getBoundingClientRect().top - 128;

        window.scrollTo({
            top: Math.max(targetY, 0),
            behavior: 'smooth',
        });
    }

    function showCardsWithoutClientPagination(matchedCards, shouldScroll) {
        const matchedSet = new Set(matchedCards);

        cards.forEach((card, index) => {
            const isVisible = matchedSet.has(card);

            card.hidden = !isVisible;
            card.classList.toggle('is-visible', isVisible);
            card.classList.remove('is-preparing');

            if (isVisible) {
                card.style.transitionDelay = `${Math.min(index * 55, 220)}ms`;
            } else {
                card.style.transitionDelay = '';
            }
        });

        if (emptyState) {
            emptyState.hidden = matchedCards.length !== 0;
        }

        if (shouldScroll) {
            scrollToNewsList();
        }
    }

    function showCardsWithClientPagination(matchedCards, shouldScroll) {
        const totalPages = Math.max(1, Math.ceil(matchedCards.length / itemsPerPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const visibleCards = matchedCards.slice(startIndex, endIndex);
        const visibleSet = new Set(visibleCards);

        cards.forEach((card) => {
            card.classList.remove('is-visible');
            card.classList.add('is-preparing');
            card.style.transitionDelay = '';

            if (!visibleSet.has(card)) {
                card.hidden = true;
                return;
            }

            card.hidden = false;
        });

        window.requestAnimationFrame(() => {
            visibleCards.forEach((card, index) => {
                card.style.transitionDelay = `${Math.min(index * 55, 220)}ms`;
                card.classList.remove('is-preparing');
                card.classList.add('is-visible');
            });
        });

        if (emptyState) {
            emptyState.hidden = matchedCards.length !== 0;
        }

        renderPagination(totalPages);

        if (shouldScroll) {
            scrollToNewsList();
        }
    }

    function updateNewsView(shouldScroll = false) {
        const matchedCards = getMatchedCards();

        if (!useClientPagination) {
            showCardsWithoutClientPagination(matchedCards, shouldScroll);
            return;
        }

        showCardsWithClientPagination(matchedCards, shouldScroll);
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            window.clearTimeout(searchTimer);

            searchTimer = window.setTimeout(() => {
                currentPage = 1;
                updateNewsView(false);
            }, 120);
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;

            event.preventDefault();
            currentPage = 1;
            updateNewsView(true);
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', function () {
            currentPage = 1;
            updateNewsView(true);
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            currentPage = 1;
            updateNewsView(false);
        });
    }

    updateNewsView(false);
}

/* ============================================================
   News Email Form
   Kalau form punya action ke controller, submit dibiarkan jalan.
   Kalau tidak punya action, tampilkan pesan lokal saja.
   ============================================================ */

function initNewsEmailForm() {
    const emailForm = document.getElementById('newsEmailForm');
    const emailInput = document.getElementById('newsEmailInput');
    const emailMessage = document.getElementById('newsEmailMessage');

    if (!emailForm || !emailInput || !emailMessage) return;

    emailForm.addEventListener('submit', function (event) {
        const action = emailForm.getAttribute('action');
        const shouldSubmitToServer = Boolean(action && action.trim() && action !== '#');

        if (shouldSubmitToServer) {
            return;
        }

        event.preventDefault();

        const email = emailInput.value.trim();

        if (!email) return;

        emailMessage.hidden = false;
        emailMessage.textContent = `Terima kasih. Update berita akan dikirim ke ${email}.`;
        emailInput.value = '';
    });
}

/* ============================================================
   Article Share
   ============================================================ */

function initArticleShare() {
    const buttons = Array.from(document.querySelectorAll('[data-share]'));

    if (!buttons.length) return;

    const pageUrl = encodeURIComponent(window.location.href);
    const pageTitle = encodeURIComponent(document.title);

    const shareUrls = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`,
        twitter: `https://twitter.com/intent/tweet?url=${pageUrl}&text=${pageTitle}`,
        whatsapp: `https://api.whatsapp.com/send?text=${pageTitle}%20${pageUrl}`,
    };

    buttons.forEach((button) => {
        button.addEventListener('click', async function () {
            const type = this.dataset.share;

            if (type === 'copy') {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    this.textContent = 'Tersalin';

                    window.setTimeout(() => {
                        this.textContent = 'Salin Link';
                    }, 1600);
                } catch (error) {
                    window.prompt('Salin link artikel:', window.location.href);
                }

                return;
            }

            if (shareUrls[type]) {
                window.open(shareUrls[type], '_blank', 'width=720,height=520');
            }
        });
    });
}

/* ============================================================
   About Page
   ============================================================ */

function initAboutPage() {
    const timelineItems = Array.from(document.querySelectorAll('.sb-about-step'));

    if (!timelineItems.length) return;

    timelineItems.forEach((item) => {
        item.addEventListener('mouseenter', function () {
            timelineItems.forEach((timelineItem) => {
                timelineItem.classList.remove('active');
            });

            this.classList.add('active');
        });
    });
}

/* ============================================================
   Global Reveal Animation
   ============================================================ */

function initGlobalReveal() {
    const revealSelectors = [
        '.sb-section-title',
        '.sb-about-left',
        '.sb-about-right',
        '.sb-programs-head',
        '.sb-program-card',
        '.sb-news-head',
        '.sb-news-card',
        '.sb-testimonial-box',
        '.sb-about-copy',
        '.sb-about-visual',
        '.sb-about-intro-grid',
        '.sb-about-card',
        '.sb-about-step',
        '.sb-article-header',
        '.sb-article-cover',
        '.sb-article-body',
        '.sb-article-side-card',
        '.sb-article-cta'
    ];

    const elements = Array.from(document.querySelectorAll(revealSelectors.join(',')))
        .filter((element) => !element.classList.contains('sb-reveal'));

    if (!elements.length) return;

    elements.forEach((element, index) => {
        element.classList.add('sb-reveal');
        element.style.transitionDelay = `${Math.min(index * 45, 240)}ms`;
    });

    if (!('IntersectionObserver' in window)) {
        elements.forEach((element) => {
            element.classList.add('is-visible');
            element.style.transitionDelay = '';
        });

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -7% 0px',
    });

    elements.forEach((element) => {
        observer.observe(element);
    });
}