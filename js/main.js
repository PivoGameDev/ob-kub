// ====================================================
// ФАЙЛ: main.js (ОСНОВНОЙ ФАЙЛ САЙТА)
// НАВИГАЦИЯ, МОДАЛЬНЫЕ ОКНА, ИНИЦИАЛИЗАЦИЯ
// ====================================================

// Отключаем восстановление позиции скролла браузером
if (window.history.scrollRestoration) {
    window.history.scrollRestoration = 'manual';
}

// Сохраняем позицию скролла при клике по внутренней ссылке
document.addEventListener('click', function(e) {
    var link = e.target.closest('a[href]');
    if (link && link.hostname === window.location.hostname) {
        var href = link.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
            sessionStorage.setItem('scrollPos_' + window.location.pathname, window.scrollY);
        }
    }
});

// Восстанавливаем позицию скролла при возврате
function restoreScrollPosition() {
    if (window.location.hash) return;
    var key = 'scrollPos_' + window.location.pathname;
    try {
        var saved = sessionStorage.getItem(key);
        if (saved !== null) {
            var y = parseInt(saved, 10);
            var offset = 100;
            var w = window.innerWidth;
            if (w < 768) {
                offset = 600;
            } else if (w < 1024) {
                offset = 250;
            } else {
                offset = 130;
            }
            window.scrollTo(0, Math.max(0, y - offset));
            sessionStorage.removeItem(key);
        }
    } catch(e) {}
}
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(restoreScrollPosition, 50);
});

// Отключаем console.log на продакшене
if (window.location.hostname !== 'localhost') {
    var noop = function(){};
    console.log = noop;
    console.warn = noop;
    console.info = noop;
}

// Toggle SEO текста
function toggleSeoText(btn) {
    var full = btn.previousElementSibling;
    var arrow = btn.querySelector('.arrow');
    if (full.style.display === 'none' || !full.style.display) {
        full.style.display = 'block';
        btn.innerHTML = 'Свернуть <span class="arrow">▲</span>';
        btn.classList.add('active');
    } else {
        full.style.display = 'none';
        btn.innerHTML = 'Читать подробнее <span class="arrow">▼</span>';
        btn.classList.remove('active');
    }
}

// Toggle FAQ
function toggleFaq(btn) {
    var answer = btn.nextElementSibling;
    var icon = btn.querySelector('.faq-icon');
    answer.classList.toggle('open');
    btn.classList.toggle('active');
    icon.textContent = icon.textContent === '▲' ? '▼' : '▲';
}
if (window.location.hostname !== 'localhost') {
    var noop = function(){};
    console.log = noop;
    console.warn = noop;
    console.info = noop;
}

// Главная функция прокрутки
function scrollToHashTarget() {
    console.log("🎯 Начинаем обработку hash...");
    
    const hash = window.location.hash;
    console.log("📌 Hash из URL:", hash);
    
    if (!hash || hash === '#') {
        console.log("❌ Нет hash для обработки");
        return;
    }
    
    const targetId = hash.substring(1);
    console.log("🔍 Ищем элемент:", targetId);
    
    // Даём время на загрузку ВСЕГО
    setTimeout(() => {
        handleTargetElement(targetId);
    }, 300); // УВЕЛИЧЕННАЯ задержка
}

// Обработка конкретного элемента
function handleTargetElement(targetId) {
    console.log("🔄 Обрабатываем:", targetId);
    
    // СПЕЦИАЛЬНЫЕ КЕЙСЫ ДЛЯ ВАШЕГО САЙТА
    if (targetId === 'equipment-brewery') {
        console.log("🍺 Это раздел пивоварен!");
        activateBrewerySection();
        return;
    }
    
    if (targetId === 'equipment-dairy') {
        console.log("🥛 Это молочное оборудование!");
        activateDairySection();
        return;
    }
    
    if (targetId === 'equipment-winery') {
        console.log("🍷 Это винодельческое оборудование!");
        activateWinerySection();
        return;
    }
    
    // Обычные якорные ссылки
    const element = document.getElementById(targetId);
    if (element) {
        console.log("✅ Элемент найден, прокручиваем...");
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        console.error("❌ Элемент не найден:", targetId);
    }
}

// АКТИВАЦИЯ РАЗДЕЛА ПИВОВАРЕН
function activateBrewerySection() {
    console.log("🍺 Переходим на страницу пивоварен...");
    window.location.href = 'beer.html';
}

// АКТИВАЦИЯ МОЛОЧНОГО ОБОРУДОВАНИЯ
function activateDairySection() {
    console.log("🥛 Переходим на страницу молочного оборудования...");
    window.location.href = 'dairy.html';
}

// АКТИВАЦИЯ ВИНОДЕЛЬЧЕСКОГО ОБОРУДОВАНИЯ
function activateWinerySection() {
    console.log("🍷 Переходим на страницу винодельческого оборудования...");
    window.location.href = 'winery.html';
}

// ОБРАБОТЧИК КЛИКОВ ПО ССЫЛКАМ
function setupClickHandlers() {
    console.log("🔗 Настройка кликов по ссылкам...");
    
    document.addEventListener('click', function(event) {
        const link = event.target.closest('a[href^="#"]');
        if (link && link.getAttribute('href') !== '#') {
            event.preventDefault();
            const hash = link.getAttribute('href');
            const targetId = hash.substring(1);
            
            console.log("🖱️ Клик по якорной ссылке:", hash);
            
            // Обновляем URL
            history.pushState(null, null, hash);
            
            // Прокручиваем
            if (targetId === 'equipment-brewery') {
                activateBrewerySection();
            } else if (targetId === 'equipment-dairy') {
                activateDairySection();
            } else if (targetId === 'equipment-winery') {
                activateWinerySection();
            } else {
                const element = document.getElementById(targetId);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }
    });
}

// === ФУНКЦИЯ ДЛЯ КНОПОК КАТЕГОРИЙ ОБОРУДОВАНИЯ ===
function showCategory(category, button) {
    console.log('КЛИКНУЛИ НА КАТЕГОРИЮ:', category);
    
    // 1. Убираем активность у всех кнопок
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // 2. Делаем эту кнопку активной
    button.classList.add('active');
    
    // 3. Скрываем ВСЕ секции оборудования
    const allSections = [
        '.storage-section', '.mixing-section', '.thermal-section',
        '.pressure-section', '.sip-section', '.heat-exchangers-section',
        '.brewery-section', '.dairy-section', '.winery-section',
        '.brewery-projects-section' // ДОБАВЛЕНО: скрываем и блок проектов
    ];
    
    allSections.forEach(selector => {
        const section = document.querySelector(selector);
        if (section) {
            section.style.display = 'none';
            section.classList.remove('active');
        }
    });
    
    // 4. Показываем нужную секцию
    let sectionToShow;
    
    if (category === 'storage') sectionToShow = '.storage-section';
    else if (category === 'mixing') sectionToShow = '.mixing-section';
    else if (category === 'thermal') sectionToShow = '.thermal-section';
    else if (category === 'pressure') sectionToShow = '.pressure-section';
    else if (category === 'sip') sectionToShow = '.sip-section';
    else if (category === 'heat-exchangers') sectionToShow = '.heat-exchangers-section';
    else if (category === 'brewery') sectionToShow = '.brewery-section';
    else if (category === 'dairy') sectionToShow = '.dairy-section';
    else if (category === 'winery') sectionToShow = '.winery-section';
    
    if (sectionToShow) {
        const section = document.querySelector(sectionToShow);
        if (section) {
            section.style.display = 'block';
            section.classList.add('active');
            
            // 5. ОСОБЫЙ СЛУЧАЙ: для пивоварен показываем и блок проектов
            if (category === 'brewery') {
                const projectsSection = document.querySelector('.brewery-projects-section');
                if (projectsSection) {
                    projectsSection.style.display = 'block';
                    projectsSection.classList.add('active');
                }
            }
            
            // 6. Прокручиваем к секции с ОТСТУПОМ
            const headerHeight = document.querySelector('.header').offsetHeight || 100;
            const yOffset = -headerHeight - 20; // Отступ в пикселях
            const y = section.getBoundingClientRect().top + window.pageYOffset + yOffset;
            
            window.scrollTo({
                top: y,
                behavior: 'smooth'
            });
            
            // 7. Подсвечиваем на секунду
            section.style.boxShadow = '0 0 0 4px #F77C2A';
            setTimeout(() => {
                section.style.boxShadow = '';
            }, 1000);
        }
    }
}

// === ЯНДЕКС.КАРТЫ ===
// ЗАМЕНИТЕ текущую функцию initYandexMap на эту простую версию:
function initYandexMap() {
    console.log('🔄 Запускаем инициализацию Яндекс.Карты...');
    
    const mapContainer = document.getElementById('yandex-map');
    if (!mapContainer) {
        console.error('❌ Не найден элемент с id="yandex-map"!');
        return;
    }

    // === ВАЖНО: ЗАЩИТА ОТ ПОВТОРНОГО ЗАПУСКА ===
    // Если карта уже создана, больше ничего не делаем
    if (window.yandexMapInitialized) {
        console.log('✅ Карта уже была инициализирована ранее, пропускаем.');
        return;
    }
    
    // Помечаем, что карта начала создаваться
    window.yandexMapInitialized = true;

    // Если уже есть карта - не инициализируем повторно
    if (mapContainer.hasChildNodes()) {
        console.log('✅ Карта уже инициализирована');
        return;
    }
    
    // Проверяем, загружена ли библиотека Яндекс.Карт
    if (typeof ymaps === 'undefined') {
        console.log('📥 Загружаем API Яндекс.Карт...');
        
        // Создаем тег скрипта
        const script = document.createElement('script');
        script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
        script.async = true;
        
        // Ждем загрузки библиотеки
        script.onload = function() {
            console.log('✅ API Яндекс.Карт загружено');
            createYandexMap();
        };
        
        script.onerror = function() {
            console.error('❌ Ошибка загрузки Яндекс.Карт');
            // Показываем заглушку
            mapContainer.innerHTML = `
                <div style="background: #f5f5f5; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 48px; margin-bottom: 10px;">🗺️</div>
                        <p style="color: #666; margin: 0;">Карта временно недоступна</p>
                        <p style="color: #888; font-size: 14px; margin-top: 5px;">Дорожный переулок, 5
посёлок Индустриальный, городской округ Краснодар
</p>
                    </div>
                </div>
            `;
        };
        
        document.head.appendChild(script);
    } else {
        // Библиотека уже загружена
        console.log('✅ API Яндекс.Карт уже загружено');
        createYandexMap();
    }
    
    // Функция создания карты
function createYandexMap() {
    try {
        ymaps.ready(function() {
            console.log('📍 Создаем карту с правильными координатами...');
            
            // ПРАВИЛЬНЫЕ КООРДИНАТЫ
            const correctCoordinates = [45.094864, 39.104650];
            
            // Создаем карту с правильными координатами
            const map = new ymaps.Map('yandex-map', {
                center: correctCoordinates, // Новые координаты
                zoom: 16,
                controls: ['zoomControl', 'fullscreenControl']
            });
            
            // Создаем метку
            const placemark = new ymaps.Placemark(correctCoordinates, {
                balloonContentHeader: 'ОБОРУДОВАНИЕ КУБАНИ',
                balloonContentBody: 'Дорожный переулок, 5<br>посёлок Индустриальный<br>городской округ Краснодар, 350032',
                balloonContentFooter: 'Производитель оборудования из нержавеющей стали'
            }, {
                preset: 'islands#orangeIcon',
                iconColor: '#F77C2A'
            });
            
            // Добавляем метку
            map.geoObjects.add(placemark);
            
            // Автоматически открываем балун
            setTimeout(() => {
                placemark.balloon.open();
            }, 1500);
            
            console.log('✅ Яндекс.Карта создана с координатами:', correctCoordinates);
        });
    } catch (error) {
        console.error('❌ Ошибка создания карты:', error);
        
        // Показываем заглушку с новым адресом
        const mapContainer = document.getElementById('yandex-map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div style="background: #f5f5f5; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 48px; margin-bottom: 10px;">🗺️</div>
                        <p style="color: #666; margin: 0;">Дорожный переулок, 5</p>
                        <p style="color: #888; font-size: 14px; margin-top: 5px;">посёлок Индустриальный, Краснодар</p>
                        <p style="color: #888; font-size: 14px; margin-top: 5px;">Координаты: 45.094864, 39.104650</p>
                    </div>
                </div>
            `;
        }
    }
}
}

// ВЫЗЫВАЕМ ФУНКЦИЮ СРАЗУ
window.addEventListener('load', function() {
    setTimeout(initYandexMap, 500);
});

// === МОДАЛЬНЫЕ ОКНА ===
// 20. Функция открытия модального окна экскурсии
function openTourModal(event) {
    console.log('Открываем экскурсию!');
    
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const modal = document.getElementById('tour-modal');
    if (!modal) {
        console.error('Окно экскурсии не найдено! Добавьте HTML код');
        return;
    }
    
    const image = modal.querySelector('#tour-modal-image');
    const title = modal.querySelector('#tour-modal-title');
    const content = modal.querySelector('#tour-modal-content');
    
    if (image) image.src = tourModalData.image;
    if (image) image.alt = tourModalData.imageAlt;
    if (title) title.textContent = tourModalData.title;
    if (content) content.innerHTML = tourModalData.content;
    
    modal.style.display = 'flex';
}

// 21. Функция закрытия модального окна экскурсии
function closeTourModal() {
    console.log('Закрываем экскурсию');
    
    const modal = document.getElementById('tour-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// 22. Функция отправки заявки на экскурсию
function submitTourRequest(event) {
    event.preventDefault();
    var form = document.getElementById('tour-request-form');
    clearFormErrors(form);
    
    var name = document.getElementById('tour-name');
    var phone = document.getElementById('tour-phone');
    var comment = document.getElementById('tour-comment');
    var privacyCheckbox = document.getElementById('tour-privacy-checkbox');
    var submitBtn = form.querySelector('button[type="submit"]');
    var hasError = false;
    
    if (!name || !name.value.trim()) {
        showFieldError(name, 'Введите имя');
        hasError = true;
    }
    
    if (!phone || !phone.value.trim()) {
        showFieldError(phone, 'Введите телефон');
        hasError = true;
    }
    
    if (privacyCheckbox && !privacyCheckbox.checked) {
        showFieldError(privacyCheckbox, 'Дайте согласие на обработку данных');
        hasError = true;
    }
    
    if (hasError) return;
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Отправка...';
    }
    
    var formData = new FormData();
    formData.append('form_type', 'tour');
    formData.append('name', name.value.trim());
    formData.append('phone', phone.value.trim());
    if (comment && comment.value.trim()) formData.append('message', comment.value.trim());
    formData.append('agreement', privacyCheckbox && privacyCheckbox.checked ? '1' : '0');
    formData.append('_csrf', btoa(String(Math.floor(Date.now() / 1000))));
    formData.append('_website', '');
    
    fetch('php/send.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showFormSuccess(form, '<strong>Заявка отправлена!</strong> Мы перезвоним вам по номеру ' + phone.value + ' в течение часа для согласования даты и времени.');
            form.reset();
            if (typeof window.trackerSend === 'function') {
                window.trackerSend({ action: 'submit', form: 'tour', page: location.pathname, sid: (localStorage.getItem('tracker_sid_v2') || '') });
            }
            if (submitBtn) {
                submitBtn.textContent = '✓ Отправлено';
                submitBtn.classList.add('sent');
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'ОТПРАВИТЬ ЗАЯВКУ';
                    submitBtn.classList.remove('sent');
                }, 3000);
            }
        } else {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'ОТПРАВИТЬ ЗАЯВКУ'; }
            showFormSuccess(form, '<strong>Ошибка:</strong> ' + (data.error || 'Попробуйте позже'));
            var msg = form.querySelector('.form-success-message');
            if (msg) msg.classList.add('error');
            setTimeout(function() {
                var msg = form.querySelector('.form-success-message.visible');
                if (msg) msg.classList.remove('visible', 'error');
            }, 8000);
        }
    })
    .catch(function() {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'ОТПРАВИТЬ ЗАЯВКУ'; }
        showFormSuccess(form, '<strong>Ошибка соединения.</strong> Проверьте подключение и попробуйте снова.');
        var msg = form.querySelector('.form-success-message');
        if (msg) msg.classList.add('error');
        setTimeout(function() {
            var msg = form.querySelector('.form-success-message.visible');
            if (msg) msg.classList.remove('visible', 'error');
        }, 8000);
    });
}

// 25. Функция открытия модального окна сертификатов
function openCertificatesModal(e) {
    console.log('Открываем сертификаты!');
    
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    const modal = document.getElementById('certificates-modal');
    if (modal) {
        modal.style.display = 'flex';
    } else {
        console.error('Окно не найдено! Добавьте HTML код');
    }
    
    return false;
}

// 26. Функция закрытия модального окна сертификатов
function closeCertificatesModal() {
    console.log('Закрываем сертификаты');
    
    const modal = document.getElementById('certificates-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// 27. Функция скачивания сертификата
function downloadCertificate(certType) {
    console.log('Скачиваем сертификат:', certType);
    
    let fileName, filePath;
    
    if (certType === 'tr-eaes') {
        fileName = '504517_Макет_ДС_ТР_ЕАЭС.docx';
        filePath = '/files/504517_Макет_ДС_ТР_ЕАЭС_(1).docx';
    } else if (certType === 'ok-2024-2026') {
        fileName = 'сертификат_ок_24-26_год.docx';
        filePath = '/files/сертификат_ок_24-26_год.docx';
    }
    
    const link = document.createElement('a');
    link.href = filePath;
    link.download = fileName;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showDownloadNotification(fileName);
}

// 28. Функция скачивания всех сертификатов
function downloadAllCertificates() {
    console.log('Скачиваем все сертификаты...');
    
    setTimeout(() => {
        downloadCertificate('tr-eaes');
    }, 100);
    
    setTimeout(() => {
        downloadCertificate('ok-2024-2026');
    }, 800);
    
    showDownloadNotification('Все сертификаты');
}

function showDownloadNotification(fileName) {
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3498db;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        ">
            <span style="font-size: 24px;">📥</span>
            <div>
                <strong style="display: block;">Скачивание началось</strong>
                <small>${fileName}</small>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 500);
    }, 2000);
}

// 30. Функция закрытия окна предпросмотра
 function closePreviewModal() {
    const modal = document.getElementById('certificate-preview-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function openPreviewModal() {
    const modal = document.getElementById('certificate-preview-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function previewCertificate(certType) {
    const content = document.getElementById('preview-content');
    if (!content) return;
    
    const previews = {
        'tr-eaes': {
            title: 'Сертификат ТР ЕАЭС',
            image: 'cert-tr-eaes.jpg',
            text: 'Сертификат соответствия требованиям Технического регламента Евразийского экономического союза'
        },
        'ok-2024-2026': {
            title: 'Сертификат соответствия 2024-2026',
            image: 'cert-ok-2024.jpg',
            text: 'Сертификат соответствия ГОСТ, ISO 9001 на 2024-2026 годы'
        }
    };
    
    const cert = previews[certType];
    if (!cert) return;
    
    content.innerHTML = `
        <div style="text-align: center; padding: 20px;">
            <h2 style="color: #2b2b39; margin-bottom: 20px;">${cert.title}</h2>
            <div style="background: #f5f5f5; border-radius: 8px; padding: 20px; margin-bottom: 20px; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                <p style="color: #666;">Предпросмотр сертификата</p>
            </div>
            <p style="color: #555; line-height: 1.5;">${cert.text}</p>
            <button onclick="downloadCertificate('${certType}')" style="margin-top: 20px; padding: 12px 30px; background: #F77C2A; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px;">
                Скачать сертификат
            </button>
        </div>
    `;
    
    openPreviewModal();
}

// 32. Функция открытия модального окна Гарантия
function openGuaranteeModal() {
    const modal = document.getElementById('guarantee-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// 33. Функция закрытия модального окна Гарантия
function closeGuaranteeModal() {
    const modal = document.getElementById('guarantee-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// 34. Функция открытия модального окна Оплата
function openPaymentModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// 35. Функция закрытия модального окна Оплата
function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// 36. Функция открытия модального окна Монтаж и запуск
function openMountingModal() {
    const modal = document.getElementById('mounting-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// 37. Функция закрытия модального окна Монтаж и запуск
function closeMountingModal() {
    const modal = document.getElementById('mounting-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// 38. Функция открытия модального окна Статьи
function openArticlesModal() {
    const modal = document.getElementById('articles-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);
        
        // Активируем первую вкладку (пиво)
        activateArticleTab('beer');
    }
}

// 39. Функция закрытия модального окна Статьи
function closeArticlesModal() {
    const modal = document.getElementById('articles-modal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// 40. Функция активации таба статей
function activateArticleTab(tabId) {
    // Убираем активный класс у всех табов
    document.querySelectorAll('.article-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Убираем активный класс у всех статей
    document.querySelectorAll('.article-content').forEach(article => {
        article.classList.remove('active');
    });
    
    // Активируем выбранный таб
    const activeTab = document.querySelector(`.article-tab[data-tab="${tabId}"]`);
    if (activeTab) {
        activeTab.classList.add('active');
    }
    
    // Показываем соответствующую статью
    const activeArticle = document.getElementById(`${tabId}-article`);
    if (activeArticle) {
        activeArticle.classList.add('active');
    }
    
    // Обновляем индикатор текущей статьи
    updateCurrentTabIndicator(tabId);
    
    // Обновляем кнопки навигации
    updateNavigationButtons(tabId);
}

// 41. Обновление индикатора текущей статьи
function updateCurrentTabIndicator(tabId) {
    const tabNames = {
        'beer': '🍺 Пиво',
        'wine': '🍷 Вино',
        'milk': '🥛 Молоко',
        'cheese': '🧀 Сыроварение',
        'curd': '🥄 Творог',
        'juice': '🧃 Соки/Нектары',
        'oil': '🫒 Масло',
        'sauce': '🍯 Майонез/Соусы',
        'automation': '🤖 Автоматизация',
        'installation': '🔧 Монтажная обвязка',
        'certification': '📋 Стандарты и сертификация'
    };
    
    const indicator = document.getElementById('current-tab-name');
    if (indicator && tabNames[tabId]) {
        indicator.textContent = tabNames[tabId];
    }
}

// 42. Обновление кнопок навигации статей
function updateNavigationButtons(tabId) {
    const tabs = ['beer', 'wine', 'milk', 'cheese', 'curd', 'juice', 'oil', 'sauce', 'automation', 'installation', 'certification'];
    const currentIndex = tabs.indexOf(tabId);
    
    const prevBtn = document.querySelector('.prev-article-btn');
    const nextBtn = document.querySelector('.next-article-btn');
    
    if (prevBtn) {
        prevBtn.disabled = currentIndex === 0;
        prevBtn.onclick = () => {
            if (currentIndex > 0) {
                activateArticleTab(tabs[currentIndex - 1]);
            }
        };
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentIndex === tabs.length - 1;
        nextBtn.onclick = () => {
            if (currentIndex < tabs.length - 1) {
                activateArticleTab(tabs[currentIndex + 1]);
            }
        };
    }
}

// 43. Функция заказа оборудования из статьи
function orderEquipment(industry) {
    closeArticlesModal();
    
    // Прокрутка к секции расчёта
    setTimeout(() => {
        const calculationSection = document.getElementById('calculation');
        if (calculationSection) {
            calculationSection.scrollIntoView({ behavior: 'smooth' });
            
            // Переключение на соответствующую вкладку
            let targetTab = 'detailed'; // по умолчанию
            
            if (industry === 'beer') {
                targetTab = 'brewery';
            } else if (industry === 'wine') {
                targetTab = 'winery';
            } else if (industry === 'milk') {
                targetTab = 'dairy';
            }
            
            const tabBtn = document.querySelector(`.tab-btn[data-tab="${targetTab}"]`);
            if (tabBtn) {
                tabBtn.click();
            }
        }
    }, 300);
}

// 44. Функция открытия модального окна проектов (ИСПРАВЛЕННАЯ)
function openProjectModal(projectId) {
    if (projectId.startsWith('project-')) {
        const num = projectId.replace('project-', '');
        const modal = document.getElementById(`project-modal-${num}`);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    else if (projectId.startsWith('brewery-')) {
        const num = projectId.replace('brewery-', '');
        const modal = document.getElementById(`brewery-modal-${num}`);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
}

function openBreweryModal(projectNum) {
    const modal = document.getElementById(`brewery-modal-${projectNum}`) || document.getElementById(`project-modal-${projectNum}`);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// 45. Функция закрытия модального окна проектов (ИСПРАВЛЕННАЯ)
function closeProjectModal(projectNum) {
    const modal = document.getElementById(`project-modal-${projectNum}`);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Дополнительная функция для закрытия пивоваренных модальных окон
function closeBreweryModal(projectNum) {
    const modal = document.getElementById(`brewery-modal-${projectNum}`);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// 46. Функция заказа аналогичного проекта
function orderSimilarProject() {
    closeProjectModal();
    setTimeout(() => {
        const calculationSection = document.getElementById('calculation');
        if (calculationSection) {
            calculationSection.scrollIntoView({ behavior: 'smooth' });
            const detailedTab = document.querySelector('.tab-btn[data-tab="detailed"]');
            if (detailedTab) {
                detailedTab.click();
            }
        }
    }, 500);
}

// 47. Функция прокрутки к примерам оборудования
function scrollToEquipmentExamples() {
    const target = document.getElementById('equipment-examples');
    if (target) {
        const headerHeight = document.querySelector('.header').offsetHeight;
        const yOffset = -headerHeight - 20;
        const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
        
        window.scrollTo({
            top: y,
            behavior: 'smooth'
        });
    }
}

// 48. Функция прокрутки к контактам
function scrollToContacts() {
    const target = document.getElementById('contacts');
    if (target) {
        const headerHeight = document.querySelector('.header').offsetHeight;
        const yOffset = -headerHeight - 20;
        const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
        
        window.scrollTo({
            top: y,
            behavior: 'smooth'
        });
    }
}

// 50. Функция прокрутки к категориям оборудования
function scrollToCategories() {
    const categoriesSection = document.getElementById('equipment-categories');
    
    if (categoriesSection) {
        const headerHeight = document.querySelector('.header').offsetHeight;
        const yOffset = -headerHeight - 20;
        
        const y = categoriesSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
        
        window.scrollTo({
            top: y,
            behavior: 'smooth'
        });
    }
}

// === ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ===
// 53. Функция исправления кнопки сертификатов
function fixCertificatesButton() {
    console.log('Ищем кнопку сертификатов...');
    
    setTimeout(() => {
        const allButtons = document.querySelectorAll('button');
        allButtons.forEach(btn => {
            const btnText = btn.textContent.toLowerCase().trim();
            
            if (btnText.includes('сертификат') || btnText.includes('сертификаты')) {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.onclick = function(e) {
                    openCertificatesModal(e);
                    return false;
                };
            }
        });
        
        const certBtns = document.querySelectorAll('.certificates-btn');
        certBtns.forEach(btn => {
            console.log('Найдена кнопка по классу');
            btn.onclick = function(e) {
                openCertificatesModal(e);
                return false;
            };
        });
        
    }, 1000);
}

// 54. Функция исправления кнопки экскурсии
function fixTourButton() {
    console.log('Ищем кнопку экскурсии...');
    
    setTimeout(() => {
        const allButtons = document.querySelectorAll('button');
        allButtons.forEach(btn => {
            const btnText = btn.textContent.toLowerCase().trim();
            
            if (btnText.includes('экскурс') || btnText.includes('тур') || btn.classList.contains('tour-btn')) {
                btn.onclick = function(e) {
                    openTourModal(e);
                    return false;
                };
            }
        });
        
    }, 100);
}

// === ФИКС: ПРАВИЛЬНАЯ ВАЛИДАЦИЯ ФОРМЫ ПОДРОБНОГО РАСЧЕТА ===
function setupDetailedFormValidation() {
    const form = document.querySelector('#detailed-form form');
    if (!form) return;
    
    // Убираем ВСЕ существующие обработчики submit
    form.onsubmit = null;
    
    // Добавляем новый обработчик, который проверяет только обязательные поля
    form.addEventListener('submit', function(e) {
        clearFormErrors(this);
        
        var name = this.querySelector('[name="name"]');
        var phone = this.querySelector('[name="phone"]');
        var checkbox = this.querySelector('[name="agreement"]');
        
        var hasError = false;
        
        if (!name || !name.value.trim()) {
            showFieldError(name, 'Введите имя');
            hasError = true;
        }
        
        if (!hasError && (!phone || !phone.value.trim())) {
            showFieldError(phone, 'Введите телефон');
            hasError = true;
        }
        
        if (!hasError && (!checkbox || !checkbox.checked)) {
            showFieldError(checkbox, 'Дайте согласие на обработку данных');
            hasError = true;
        }
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
}

// Функция для просмотра полноразмерных фотографий
function openFullImage(imageSrc) {
    const modal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');
    if (modal && modalImage) {
        modalImage.src = imageSrc;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeFullImage() {
    const modal = document.getElementById('image-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Закрытие по клику вне изображения
document.getElementById('image-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFullImage();
    }
});

function scrollToCalculationForm(lotNumber = null, event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const calculationSection = document.getElementById('calculation');
    if (calculationSection) {
        calculationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => {
            // ИЗМЕНЕНИЕ ТУТ: меняем brewery на detailed
            const detailedTab = document.querySelector('.tab-btn[data-tab="detailed"]');
            if (detailedTab) {
                activateTab('detailed'); // Активируем вкладку "Подробный расчет"
                
                // Добавляем подсветку для наглядности
                const detailedForm = document.getElementById('detailed-form');
                if (detailedForm) {
                    detailedForm.style.boxShadow = '0 0 0 3px rgba(247, 124, 42, 0.3)';
                    detailedForm.style.transition = 'box-shadow 0.5s ease';
                    
                    setTimeout(() => {
                        detailedForm.style.boxShadow = 'none';
                    }, 1500);
                }
            }
        }, 300);
    }
}

function scrollToAutomationForm(event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const calculationSection = document.getElementById('calculation');
    
    if (calculationSection) {
        calculationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        setTimeout(() => {
            const quickTabBtn = document.querySelector('.tab-btn[data-tab="quick"]');
            const quickForm = document.getElementById('quick-form');
            
            if (quickTabBtn && quickForm) {
                // Активируем вкладку "Быстрая заявка"
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                quickTabBtn.classList.add('active');
                quickForm.classList.add('active');
                quickForm.style.display = 'block';
                
                // Автоматически заполняем поле комментария
                const commentField = document.getElementById('quick-comment');
                if (commentField) {
                    commentField.value = "Здравствуйте! Меня интересует автоматизация производства. Пожалуйста, рассчитайте стоимость и позвоните мне для обсуждения деталей.";
                    
                    // Фокус на поле комментария
                    setTimeout(() => {
                        commentField.focus();
                        commentField.select();
                    }, 500);
                }
                
                // Подсветка формы
                quickForm.style.boxShadow = '0 0 0 3px rgba(247, 124, 42, 0.3)';
                quickForm.style.transition = 'box-shadow 0.5s ease';
                
                setTimeout(() => {
                    quickForm.style.boxShadow = 'none';
                }, 2000);
                
                console.log('✅ Вкладка "Быстрая заявка" активирована с текстом об автоматизации');
            } else {
                // Если не нашли быструю заявку, идем к обычной форме расчета
                scrollToCalculationForm();
            }
        }, 500);
    }
}

// Функции для модального окна игры
function openGameModal() {
    const modal = document.getElementById('game-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeGameModal() {
    const modal = document.getElementById('game-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function launchGame() {
    // Открываем игру в новой вкладке
    window.open('https://pivogamedev.github.io/steel-equipment-game/', '_blank');
    
    // Закрываем модальное окно
    closeGameModal();
    
    // Можно добавить отслеживание (по желанию)
    console.log('🎮 Игра запущена в новой вкладке');
}

// ====================================================
// ОСНОВНОЙ КОД ИНИЦИАЛИЗАЦИИ
// ====================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log("=== НАЧАЛО ИНИЦИАЛИЗАЦИИ ===");
    
    // === НАСТРОЙКА ПО УМОЛЧАНИЮ ===
    const storageBtn = document.querySelector('.category-btn[data-category="storage"]');
    const storageSection = document.querySelector('.storage-section');
    const breweryConfigContent = document.querySelector('.brewery-config-content');
    const equipmentConfigSection = document.querySelector('.equipment-configuration-section');
    const configInfoSections = document.querySelectorAll('.config-info-section');
    const brewerySection = document.querySelector('.brewery-section');
    const dairySection = document.querySelector('.dairy-section');
    const winerySection = document.querySelector('.winery-section');
    
    const allEquipmentSections = document.querySelectorAll(
    '.storage-section, .mixing-section, .thermal-section, ' +
    '.pressure-section, .sip-section, .heat-exchangers-section, ' +
    '.brewery-section, .dairy-section, .winery-section'
);
    
    // На index.html есть категории, на beer.html — нет
    // Если нет storageSection — мы на странице пивоварен, ничего не скрываем
    if (storageSection) {
        // Скрываем все блоки пивоварен
        if (breweryConfigContent) breweryConfigContent.style.display = 'none';
        if (equipmentConfigSection) equipmentConfigSection.style.display = 'none';
        configInfoSections.forEach(section => {
            section.style.display = 'none';
            section.classList.remove('active');
        });
        
        // Скрываем секцию пивоварен
        if (brewerySection) {
            brewerySection.style.display = 'none';
            brewerySection.classList.remove('active');
        }
        
        // Скрываем секцию молочного оборудования
        if (dairySection) {
            dairySection.style.display = 'none';
            dairySection.classList.remove('active');
        }
        
        // Скрываем секцию винодельни
        if (winerySection) {
            winerySection.style.display = 'none';
            winerySection.classList.remove('active');
        }
        
        // Скрываем все секции оборудования
        allEquipmentSections.forEach(section => {
            section.style.display = 'none';
            section.classList.remove('active');
        });
        
        // Сбрасываем активность всех кнопок
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        
        // Показываем Хранилище по умолчанию
        if (storageBtn && storageSection) {
            storageBtn.classList.add('active');
            storageSection.style.display = 'block';
            storageSection.classList.add('active');
            console.log("Активировано Хранилище по умолчанию");
        }
    } else {
        console.log("Страница пивоварен: секции не скрываем");
    }
    
    // === ПЕРЕХОД ИЗ ЛОТОВ ПИВОВАРЕН В ФОРМУ РАСЧЕТА ===
    const lotCalculateButtons = document.querySelectorAll('.config-calculate-btn');
    lotCalculateButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("Клик по кнопке Рассчитать в лоте");
            const configSection = this.closest('.config-info-section');
            let lotNumber = '';
            if (configSection && configSection.id) {
                lotNumber = configSection.id.replace('-info', '');
                console.log("Определен лот:", lotNumber);
            }
            scrollToCalculationForm(lotNumber);
        });
    });
    
    // === ОБРАБОТКА ВКЛАДКИ "РАСЧЕТ ПИВОВАРНИ" ===
    const breweryTabBtn = document.querySelector('.tab-btn[data-tab="brewery"]');
    if (breweryTabBtn) {
        breweryTabBtn.addEventListener('click', function() {
            console.log("Активирована вкладка пивоварни");
            activateTab('brewery');
        });
    }
    
    // === ОБРАБОТКА ВЫБОРА ЛОТА В ФОРМЕ ===
    const lotSelect = document.getElementById('brewery-lot-select');
    if (lotSelect) {
        lotSelect.addEventListener('change', function() {
            console.log("Выбран лот в форме:", this.value);
            const equipmentContainer = document.getElementById('brewery-equipment-container');
            if (this.value) {
                equipmentContainer.style.display = 'block';
                if (typeof loadBreweryLotData === 'function') {
                    loadBreweryLotData(this.value);
                }
                if (typeof updateSelectedSummary === 'function') {
                    updateSelectedSummary();
                }
            } else {
                equipmentContainer.style.display = 'none';
            }
        });
    }
    
    // === КНОПКА ДОБАВЛЕНИЯ ЦКТ ===
    const addCKTButton = document.querySelector('.add-ckt-btn');
    if (addCKTButton) {
        addCKTButton.addEventListener('click', function() {
            console.log("Добавление дополнительного ЦКТ");
            if (typeof addAdditionalCKT === 'function') {
                addAdditionalCKT();
            }
        });
    }
    
    // === КНОПКА "РАССЧИТАТЬ ОБОРУДОВАНИЕ ДЛЯ ПИВОВАРНИ" ===
    const mainBreweryBtn = document.querySelector('.brewery-calculate-btn');
    if (mainBreweryBtn) {
        mainBreweryBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Клик по основной кнопке пивоварни");
            scrollToCalculationForm();
        });
    }
    
    // === КНОПКА "РАССЧИТАТЬ ОБОРУДОВАНИЕ ДЛЯ ВИНОДЕЛЬНИ" ===
    const mainWineryBtn = document.querySelector('.winery-calculate-btn');
    if (mainWineryBtn) {
        mainWineryBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("Клик по кнопке винодельни");
            if (typeof scrollToWineryCalculation === 'function') {
                scrollToWineryCalculation();
            }
        });
    }
    
    // === ПЕРЕКЛЮЧЕНИЕ ВКЛАДОК ФОРМЫ РАСЧЕТА ===
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            console.log("Переключение на вкладку:", tabId);
            if (typeof activateTab === 'function') {
                activateTab(tabId);
            }
        });
    });
    
    // === КОПИРОВАНИЕ EMAIL В ШАПКЕ ===
    const copyEmailBtn = document.querySelector('.copy-email-btn-full');
    if (copyEmailBtn) {
        copyEmailBtn.addEventListener('click', function() {
            const email = 'oborudovanie-kubani@yandex.ru';
            navigator.clipboard.writeText(email).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Скопировано!';
                this.style.background = '#27ae60';
                this.style.borderColor = '#27ae60';
                this.style.color = 'white';
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.style.borderColor = '';
                    this.style.color = '';
                }, 2000);
            });
        });
    }
    
    // === НАВИГАЦИЯ СТРЕЛКАМИ ПРОЕКТОВ ===
    const nextArrow = document.querySelector('.nav-arrow.next');
    const prevArrow = document.querySelector('.nav-arrow.prev');
    const projectsContainer = document.querySelector('.projects-container');
    if (nextArrow && projectsContainer) {
        nextArrow.addEventListener('click', function() {
            projectsContainer.scrollBy({ left: 830, behavior: 'smooth' });
        });
    }
    if (prevArrow && projectsContainer) {
        prevArrow.addEventListener('click', function() {
            projectsContainer.scrollBy({ left: -830, behavior: 'smooth' });
        });
    }
    
    // === НАВИГАЦИЯ ПИВОВАРЕННЫХ ПРОЕКТОВ ===
    const breweryNextArrow = document.querySelector('.brewery-nav-arrow.next');
    const breweryPrevArrow = document.querySelector('.brewery-nav-arrow.prev');
    const breweryProjectsContainer = document.querySelector('.brewery-projects-container');
    if (breweryNextArrow && breweryProjectsContainer) {
        breweryNextArrow.addEventListener('click', function() {
            breweryProjectsContainer.scrollBy({ left: 730, behavior: 'smooth' });
        });
    }
    if (breweryPrevArrow && breweryProjectsContainer) {
        breweryPrevArrow.addEventListener('click', function() {
            breweryProjectsContainer.scrollBy({ left: -730, behavior: 'smooth' });
        });
    }
    
    // === ПЕРЕКЛЮЧЕНИЕ КАТЕГОРИЙ ОБОРУДОВАНИЯ ===
    const categoryButtons = document.querySelectorAll('.category-btn');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            console.log('Выбрана категория:', category);
            
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            allEquipmentSections.forEach(section => {
                section.classList.remove('active');
                section.style.display = 'none';
            });
            
            if (breweryConfigContent) breweryConfigContent.style.display = 'none';
            if (equipmentConfigSection) equipmentConfigSection.style.display = 'none';
            configInfoSections.forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            
            if (category === 'brewery') {
                if (breweryConfigContent) breweryConfigContent.style.display = 'block';
                if (equipmentConfigSection) equipmentConfigSection.style.display = 'block';
                
                if (brewerySection) {
                    brewerySection.style.display = 'block';
                    brewerySection.classList.add('active');
                }
                
                setTimeout(() => {
                    const lot1Btn = document.querySelector('.config-btn[data-config="lot1"]');
                    const lot1Info = document.getElementById('lot1-info');
                    const configSections = document.querySelectorAll('.config-info-section');
                    if (!lot1Btn || !lot1Info) return;
                    configSections.forEach(section => {
                        section.style.display = 'none';
                        section.classList.remove('active');
                    });
                    lot1Info.style.display = 'block';
                    lot1Info.classList.add('active');
                    document.querySelectorAll('.config-btn').forEach(btn => btn.classList.remove('active'));
                    lot1Btn.classList.add('active');
                }, 100);
            } else if (category === 'dairy') {
                if (dairySection) {
                    dairySection.style.display = 'block';
                    dairySection.classList.add('active');
                }
            } else if (category === 'winery') {
                if (winerySection) {
                    winerySection.style.display = 'block';
                    winerySection.classList.add('active');
                }
            } else {
                let targetSection;
                if (category === 'maturation') {
                    targetSection = document.querySelector('.thermal-section');
                } else if (category === 'raw-material') {
                    targetSection = document.querySelector('.sip-section');
                } else if (category === 'preparation') {
                    targetSection = document.querySelector('.heat-exchangers-section');
                } else {
                    targetSection = document.querySelector(`.${category}-section`);
                }
                
                if (targetSection) {
                    targetSection.style.display = 'block';
                    targetSection.classList.add('active');
                }
            }
        });
    });
    
    // === ПЕРЕКЛЮЧЕНИЕ ЛОТОВ ПИВОВАРЕН ===
    const configButtons = document.querySelectorAll('.config-btn');
    configButtons.forEach(button => {
        button.addEventListener('click', function() {
            const configId = this.getAttribute('data-config');
            configButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const configSections = document.querySelectorAll('.config-info-section');
            configSections.forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            
            const targetSection = document.getElementById(`${configId}-info`);
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('active');
            }
        });
    });
    
    // === ОБРАБОТЧИК ДЛЯ КНОПКИ КОПИРОВАНИЯ EMAIL В КОНТАКТАХ ===
    const contactsCopyBtn = document.querySelector('.contacts-copy-btn');
    if (contactsCopyBtn) {
        contactsCopyBtn.addEventListener('click', function() {
            const email = 'oborudovanie-kubani@yandex.ru';
            navigator.clipboard.writeText(email).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Скопировано!';
                this.style.background = '#27ae60';
                this.style.borderColor = '#27ae60';
                this.style.color = 'white';
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.style.borderColor = '';
                    this.style.color = '';
                }, 2000);
            });
        });
    }
    
    // === ОБРАБОТЧИКИ ДЛЯ МОЛОЧНОГО ОБОРУДОВАНИЯ ===
    const dairyCalculateBtn = document.querySelector('.dairy-calculate-btn');
    if (dairyCalculateBtn) {
        dairyCalculateBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation();
            console.log('Переход к расчету молочного оборудования');
            if (typeof scrollToDairyCalculation === 'function') {
                scrollToDairyCalculation();
            }
        });
    }
    
    const dairyAddBtn = document.querySelector('.dairy-add-equipment-btn');
    if (dairyAddBtn) {
        dairyAddBtn.addEventListener('click', function() {
            console.log('Добавление молочного оборудования');
            if (typeof addDairyEquipmentCard === 'function') {
                addDairyEquipmentCard();
            }
        });
    }
    
    const dairyTabBtn = document.querySelector('.tab-btn[data-tab="dairy"]');
    if (dairyTabBtn) {
        dairyTabBtn.addEventListener('click', function() {
            if (typeof activateTab === 'function') {
                activateTab('dairy');
            }
        });
    }
    
    // === ОБРАБОТЧИКИ ДЛЯ ВИНОДЕЛЬЧЕСКОГО ОБОРУДОВАНИЯ ===
    const wineryCalculateBtn = document.querySelector('.winery-calculate-btn');
    if (wineryCalculateBtn) {
        wineryCalculateBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation();
            console.log('Переход к расчету винодельческого оборудования');
            if (typeof scrollToWineryCalculation === 'function') {
                scrollToWineryCalculation();
            }
        });
    }
    
    const wineryAddBtn = document.querySelector('.winery-add-equipment-btn');
    if (wineryAddBtn) {
        wineryAddBtn.addEventListener('click', function() {
            console.log('Добавление винодельческого оборудования');
            if (typeof addWineryEquipmentCard === 'function') {
                addWineryEquipmentCard();
            }
        });
    }
    
    const wineryTabBtn = document.querySelector('.tab-btn[data-tab="winery"]');
    if (wineryTabBtn) {
        wineryTabBtn.addEventListener('click', function() {
            if (typeof activateTab === 'function') {
                activateTab('winery');
            }
        });
    }
    
    // Инициализация молочной формы
    setTimeout(() => {
        if (typeof setupDairyEquipmentSelect === 'function') {
            setupDairyEquipmentSelect();
        }
    }, 500);
    
    // Инициализация винодельческой формы
    setTimeout(() => {
        if (typeof setupWineryEquipmentSelect === 'function') {
            setupWineryEquipmentSelect();
        }
    }, 500);
    
    // === НАСТРОЙКА ФОРМЫ ПОДРОБНОГО РАСЧЕТА ===
    setTimeout(() => {
        setupDetailedFormValidation();
    }, 1000);
    
    // === ОБРАБОТЧИКИ ДЛЯ МОДАЛЬНОГО ОКНА ПРОЕКТОВ ===
    // Назначаем обработчики для кнопок "Подробнее" в основных проектах
    document.querySelectorAll('.projects-container .details-btn').forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const projectNum = index + 1;
            const modal = document.getElementById(`project-modal-${projectNum}`);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Назначаем обработчики для кнопок "Подробнее" в проектах пивоварен
    document.querySelectorAll('.brewery-projects-container .brewery-details-btn').forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const projectNum = index + 1;
            const modal = document.getElementById(`brewery-modal-${projectNum}`);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Закрытие по кнопке "X" в проектах
    const closeProjectBtn = document.querySelector('#projects-modal .close-modal-btn');
    if (closeProjectBtn) {
        closeProjectBtn.addEventListener('click', closeProjectModal);
    }
    
    // Закрытие по кнопке "Назад к проектам"
    const modalBackBtn = document.querySelector('#projects-modal .modal-back-btn');
    if (modalBackBtn) {
        modalBackBtn.addEventListener('click', closeProjectModal);
    }
    
    // Кнопка "Заказать такое же оборудование"
    const modalOrderBtn = document.querySelector('#projects-modal .modal-order-btn');
    if (modalOrderBtn) {
        modalOrderBtn.addEventListener('click', orderSimilarProject);
    }
    
    // === НАСТРОЙКА ФОРМ ПИВОВАРНИ, МОЛОЧНОЙ И ВИНОДЕЛЬЧЕСКОЙ ===
    setTimeout(() => {
        if (typeof setupBreweryForm === 'function') {
            setupBreweryForm();
        }
        if (typeof setupWineryForm === 'function') {
            setupWineryForm();
        }
    }, 500);

    // Инициализация Яндекс.Карт
    setTimeout(initYandexMap, 1000);
    
    // Инициализация кнопок комплектаций пивоварен
    setTimeout(() => {
        if (typeof initBreweryConfigButtons === 'function') {
            initBreweryConfigButtons();
        }
    }, 800);
    
    // Назначаем обработчики для всех кнопок "Игра"
    setTimeout(() => {
        const gameButtons = [
            document.querySelector('.automation-btn-secondary'),
            document.querySelector('.brewery-game-btn'),
            document.querySelector('.footer-link[href="#"]')
        ];
        
        gameButtons.forEach(btn => {
            if (btn) {
                btn.onclick = function(e) {
                    e.preventDefault();
                    openGameModal();
                };
            }
        });
    }, 500);
    
    // Инициализация существующих емкостей
    if (typeof initializeExistingTanks === 'function') {
        initializeExistingTanks();
    }
    
    // Настраиваем якорные ссылки
    setupClickHandlers();
    
    // Запускаем проверку hash
    setTimeout(scrollToHashTarget, 100);
    
    console.log("=== ЗАВЕРШЕНИЕ ИНИЦИАЛИЗАЦИИ ===");
});

// Закрытие модальных окон при клике вне их
window.addEventListener('click', function(e) {
    // Основные проекты
    for (let i = 1; i <= 5; i++) {
        const modal = document.getElementById(`project-modal-${i}`);
        if (modal && modal.style.display === 'flex' && e.target.classList.contains('project-modal-overlay')) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    // Пивоваренные проекты
    for (let i = 1; i <= 3; i++) {
        const modal = document.getElementById(`brewery-modal-${i}`);
        if (modal && modal.style.display === 'flex' && e.target.classList.contains('project-modal-overlay')) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
});

// Привяжите обработчик через JavaScript для кнопки статей в футере
document.addEventListener('DOMContentLoaded', function() {
    var articlesBtn = document.getElementById('footer-articles-btn');
    if (articlesBtn) {
        articlesBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modal = document.getElementById('articles-modal');
            document.querySelectorAll('[id*="modal"]').forEach(m => m.style.display = 'none');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            activateArticleTab('beer');
            
            return false;
        };
    }
});

// Цели Яндекс.Метрики для телефонов
document.addEventListener('DOMContentLoaded', function() {
    const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
    
    phoneLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (typeof ym !== 'undefined') {
                ym(109477134, 'reachGoal', 'phone_click');
            }
        });
    });
});

// === ЭКСПОРТ ВСЕХ ФУНКЦИЙ В ГЛОБАЛЬНУЮ ОБЛАСТЬ ===

window.scrollToCalculationForm = scrollToCalculationForm;
window.scrollToAutomationForm = scrollToAutomationForm;
window.showCategory = showCategory;
window.openTourModal = openTourModal;
window.closeTourModal = closeTourModal;
window.submitTourRequest = submitTourRequest;
 window.openCertificatesModal = openCertificatesModal;
window.closeCertificatesModal = closeCertificatesModal;
window.downloadCertificate = downloadCertificate;
window.downloadAllCertificates = downloadAllCertificates;
window.openPreviewModal = openPreviewModal;
window.closePreviewModal = closePreviewModal;
window.previewCertificate = previewCertificate;
window.openGuaranteeModal = openGuaranteeModal;
window.closeGuaranteeModal = closeGuaranteeModal;
window.openPaymentModal = openPaymentModal;
window.closePaymentModal = closePaymentModal;
window.openMountingModal = openMountingModal;
window.closeMountingModal = closeMountingModal;
window.openArticlesModal = openArticlesModal;
window.closeArticlesModal = closeArticlesModal;
window.activateArticleTab = activateArticleTab;
window.orderEquipment = orderEquipment;
window.openProjectModal = openProjectModal;
window.openBreweryModal = openBreweryModal;
window.closeProjectModal = closeProjectModal;
window.orderSimilarProject = orderSimilarProject;
window.scrollToEquipmentExamples = scrollToEquipmentExamples;
window.scrollToContacts = scrollToContacts;
window.scrollToCategories = scrollToCategories;
window.fixCertificatesButton = fixCertificatesButton;
window.fixTourButton = fixTourButton;
window.openFullImage = openFullImage;
window.closeFullImage = closeFullImage;
window.openGameModal = openGameModal;
window.closeGameModal = closeGameModal;
window.launchGame = launchGame;

console.log("✅ Основной скрипт загружен и готов к работе");