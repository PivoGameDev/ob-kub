console.log("🔥 Якорные ссылки инициализированы");
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
    console.log("🚀 Активируем раздел пивоварен...");
    
    // 1. Находим и кликаем кнопку категории "Пивоварни"
    const breweryBtn = document.querySelector('.category-btn[data-category="brewery"]');
    if (!breweryBtn) {
        console.error("❌ Кнопка пивоварен не найдена!");
        return;
    }
    
    console.log("🖱️ Нажатие на кнопку пивоварен...");
    breweryBtn.click();
    
    // 2. Ждём пока откроется секция
    setTimeout(() => {
        const brewerySection = document.querySelector('.brewery-section');
        if (brewerySection && brewerySection.style.display !== 'none') {
            console.log("✅ Секция пивоварен открыта, прокручиваем...");
            brewerySection.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
        } else {
            // Принудительно показываем и прокручиваем
            console.log("🔄 Принудительное отображение секции...");
            if (brewerySection) {
                brewerySection.style.display = 'block';
                brewerySection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }, 300);
}

// АКТИВАЦИЯ МОЛОЧНОГО ОБОРУДОВАНИЯ
function activateDairySection() {
    console.log("🥛 Активируем молочное оборудование...");
    
    const dairyBtn = document.querySelector('.category-btn[data-category="dairy"]');
    if (dairyBtn) {
        dairyBtn.click();
        
        setTimeout(() => {
            const dairySection = document.querySelector('.dairy-section');
            if (dairySection) {
                dairySection.scrollIntoView({ behavior: 'smooth' });
            }
        }, 800);
    }
}

// АКТИВАЦИЯ ВИНОДЕЛЬЧЕСКОГО ОБОРУДОВАНИЯ
function activateWinerySection() {
    console.log("🍷 Активируем винодельческое оборудование...");
    
    const wineryBtn = document.querySelector('.category-btn[data-category="winery"]');
    if (wineryBtn) {
        wineryBtn.click();
        
        setTimeout(() => {
            const winerySection = document.querySelector('.winery-section');
            if (winerySection) {
                winerySection.scrollIntoView({ behavior: 'smooth' });
            }
        }, 800);
    }
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

// === ЗАПУСК ВСЕГО ===
document.addEventListener('DOMContentLoaded', function() {
    console.log("📄 DOM готов!");
    
    // Настраиваем клики
    setupClickHandlers();
    
    // Запускаем проверку hash
    setTimeout(scrollToHashTarget, 100);
});

// Данные для лотов пивоварен (остаются без изменений)
const breweryLotsData = {
    lot1: {
        name: "250 л",
        volume: "250 л",
        production: "1,500-10,000 л/месяц",
        term: "30 дней",
        brewing: [
            "Заторный аппарат, совмещенный с фильтрационным аппаратом объем 250л",
            "Сусловарочный аппарат, совмещенный с вирпулом объем 250л",
            "Бак горячей воды 500л"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насос",
            "Ручная запорная арматура",
            "Электро парогенератор 20 кг пара/час",
            "Холодильный агрегат 8кВт",
            "Теплообменник пластинчатый 300л/с",
            "Дробилка солода 100кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль"
        ],
        ckt: {
            volume: "500",
            count: 3
        }
    },
    
    lot2: {
        name: "500 л",
        volume: "500 л",
        production: "4,000-25,000 л/месяц",
        term: "35 дней",
        brewing: [
            "Заторно-сусловарочный аппарат 500л (2 раздельные рубашки нагрева)",
            "Фильтрационный аппарат (мешалка с мотор редуктором, разборное сито)",
            "Гидроциклонный аппарат",
            "Бак горячей воды 1000л (1 рубашка нагрева)"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насосы: 4 шт",
            "Ручная запорная арматура",
            "Парогенератор 100 кг/час",
            "Холодильный агрегат 12кВт",
            "Теплообменник пластинчатый 600л/ч",
            "Дробилка солода 200кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль"
        ],
        ckt: {
            volume: "1000",
            count: 3
        }
    },
    
    lot3: {
        name: "1000 л",
        volume: "1000 л",
        production: "8,000-50,000 л/месяц",
        term: "40 дней",
        brewing: [
            "Заторно-сусловарочный аппарат 1000л (2 раздельные рубашки нагрева)",
            "Фильтрационный аппарат (мешалка с мотор редуктором, разборное сито, выгрузной люк)",
            "Гидроциклонный аппарат",
            "Бак горячей воды 2000л (1 рубашка нагрева)"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насосы: 5 шт",
            "Ручная запорная арматура",
            "Парогенератор 100 кг/час",
            "Холодильный агрегат 12кВт",
            "Теплообменник пластинчатый 1000л/ч",
            "Дробилка солода 300кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль"
        ],
        ckt: {
            volume: "2000",
            count: 3
        }
    },
    
    lot4: {
        name: "1000+ л",
        volume: "1000 л",
        production: "12,000-90,000 л/месяц",
        term: "45 дней",
        brewing: [
            "Заторный аппарат 1000л (2 раздельные рубашки нагрева)",
            "Сусловарочный аппарат 1000л (2 раздельные рубашки нагрева)",
            "Фильтрационный аппарат (мешалка с мотор редуктором, разборное сито, выгрузной люк)",
            "Гидроциклонный аппарат",
            "Бак горячей воды 2000л (1 рубашки нагрева)"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насосы: 6 шт",
            "Ручная запорная арматура",
            "Парогенератор 150 кг/час",
            "Холодильный агрегат 12кВт",
            "Теплообменник пластинчатый 1000л/ч",
            "Дробилка солода 300кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль"
        ],
        ckt: {
            volume: "3000",
            count: 3
        }
    },
    
    lot5: {
        name: "3000 л",
        volume: "3000 л",
        production: "24,000-180,000 л/месяц",
        term: "60 дней",
        brewing: [
            "Заторно-сусловарочный аппарат 3000л (2 раздельные рубашки нагрева)",
            "Фильтрационный аппарат (мешалка с мотор редуктором, разборное сито, выгрузной люк)",
            "Гидроциклонный аппарат",
            "Бак горячей воды 6000л (1 рубашки нагрева)"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насосы: 6 шт",
            "Автоматическая запорная арматура",
            "Парогенератор 300 кг/час",
            "Холодильный агрегат 20кВт",
            "Теплообменник пластинчатый 3000л/ч",
            "Дробилка солода 500кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль"
        ],
        ckt: {
            volume: "6000",
            count: 3
        }
    },
    
    lot6: {
        name: "3000+ л",
        volume: "3000 л",
        production: "47,000-350,000 л/месяц",
        term: "75 дней",
        brewing: [
            "Заторно-сусловарочный аппарат 3000л (2 раздельные рубашки нагрева)",
            "Фильтрационный аппарат (мешалка с мотор редуктором, разборное сито, выгрузной люк)",
            "Гидроциклонный аппарат",
            "Бак горячей воды 6000л (1 рубашки нагрева)"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насосы: 6 шт",
            "Автоматическая запорная арматура",
            "Парогенератор 400 кг/час",
            "Холодильный агрегат 50кВт",
            "Теплообменник пластинчатый 3000л/ч",
            "Дробилка солода 500кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль",
            "CIP система"
        ],
        ckt: {
            volume: "12000",
            count: 3
        }
    },
    
    lot7: {
        name: "5000 л",
        volume: "5000 л",
        production: "160,000-1,000,000 л/месяц",
        term: "90 дней",
        brewing: [
            "Заторно-сусловарочный аппарат 5000л (2 раздельные рубашки нагрева)",
            "Фильтрационный аппарат (мешалка с мотор редуктором, разборное сито, выгрузной люк)",
            "Гидроциклонный аппарат",
            "Бак горячей воды 10,000л (2 рубашки нагрева)"
        ],
        fermentation: [
            "Теплоизоляция",
            "Шпунт аппарат",
            "3 дисковых затвора"
        ],
        auxiliary: [
            "Насосы: 7 шт",
            "Автоматическая запорная арматура",
            "Парогенератор 700 кг/час",
            "Холодильный агрегат 150кВт",
            "Теплообменник пластинчатый 5000л/ч",
            "Дробилка солода 1000кг/ч"
        ],
        automation: [
            "Автоматика управления варочным порядком и ферментационным цехом",
            "Пневмо клапана на пар и пропиленгликоль",
            "Полная CIP система"
        ],
        ckt: {
            volume: "40000",
            count: 3
        }
    }
};

// Данные для молочного оборудования
const dairyEquipmentData = {
    'reception': { name: 'Емкости для приемки молока', defaultVolume: 5000 },
    'cooling': { name: 'Емкости для охлаждения молока', defaultVolume: 3000 },
    'processing': { name: 'Емкости для переработки молока', defaultVolume: 2000 },
    'ripening': { name: 'Стеллажи для созревания сыра', defaultVolume: null },
    'salting': { name: 'Контейнеры для соления сыра', defaultVolume: 1000 },
    'cheese-maker': { name: 'Сыроизготовители закрытого типа', defaultVolume: 1500 },
    'curd-maker': { name: 'Творогоизготовители', defaultVolume: 1000 },
    'melting': { name: 'Жиротопки', defaultVolume: 500 },
    'cheese-cooker': { name: 'Сыроварни', defaultVolume: 800 },
    'fermentation': { name: 'Ферментационные танки', defaultVolume: 3000 },
    'storage': { name: 'Емкости для хранения готовой продукции', defaultVolume: 10000 },
    'pasteurizer': { name: 'Пастеризаторы/охладители', defaultVolume: 2000 },
    'separator': { name: 'Сепараторы молочные', defaultVolume: null }
};

// Данные для винодельческого оборудования
const wineryEquipmentData = {
    'fermentation': { name: 'Емкости для ферментации вина', defaultVolume: 5000 },
    'storage': { name: 'Цистерны для выдержки и хранения', defaultVolume: 10000 },
    'press': { name: 'Прессы виноградные', defaultVolume: null },
    'sulfitation': { name: 'Емкости для сульфитации', defaultVolume: 2000 },
    'clarification': { name: 'Танки для холодного осветления', defaultVolume: 3000 },
    'filtration': { name: 'Фильтрационное оборудование', defaultVolume: null },
    'blending': { name: 'Емкости для купажирования', defaultVolume: 5000 },
    'pasteurizer': { name: 'Пастеризаторы для вина', defaultVolume: 2000 },
    'thermal': { name: 'Терморегулируемые емкости', defaultVolume: 4000 },
    'control': { name: 'Контрольно-измерительные системы', defaultVolume: null }
};

// Данные для модального окна экскурсии
const tourModalData = {
    title: "ЭКСКУРСИЯ В НАШ ЦЕХ",
    image: "factory-tour.jpg",
    imageAlt: "Наш производственный цех",
    content: `
        <p>Мы убеждены: лучший способ оценить качество — увидеть всё своими глазами. Именно поэтому мы настаиваем на экскурсии в наш производственный цех.</p>
        
        <h4>Что вы увидите:</h4>
        <div style="margin: 20px 0;">
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <span style="color: #F77C2A; margin-right: 10px; font-size: 18px; margin-top: 2px;">✓</span>
                <div><strong>Передовое оборудование</strong> — современные станки лазерной резки, аппараты аргонодуговой сварки, контрольно-измерительную лабораторию</div>
            </div>
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <span style="color: #F77C2A; margin-right: 10px; font-size: 18px; margin-top: 2px;">✓</span>
                <div><strong>Производственный процесс</strong> — полный цикл изготовления емкостей, контроль качества на каждом этапе, работу опытных мастеров (стаж от 10 лет)</div>
            </div>
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <span style="color: #F77C2A; margin-right: 10px; font-size: 18px; margin-top: 2px;">✓</span>
                <div><strong>Готовые образцы</strong> — емкости разных объемов, примеры сварочных швов, комплектующие INOXPA в сборе</div>
            </div>
        </div>
        
        <h4>Дополнительная возможность:</h4>
        <p>Мы можем организовать для вас экскурсию на предприятия наших действующих клиентов. Увидите наше оборудование в работе непосредственно в вашей отрасли — молочной, пивоваренной или винодельческой промышленности.</p>
        
        <!-- ПОЛНАЯ ФОРМА ЗАЯВКИ -->
        <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px; border: 1px solid #e0e0e0;">
            <h4 style="margin-top: 0; color: #2b2b39;">ЗАПИСАТЬСЯ НА ЭКСКУРСИЮ</h4>
            
            <form id="tour-request-form" onsubmit="submitTourRequest(event); return false;" style="display: flex; flex-direction: column; gap: 15px;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" 
                           placeholder="Ваше имя" 
                           required 
                           style="flex: 1; min-width: 200px; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;"
                           id="tour-name">
                    <input type="tel" 
                           placeholder="Телефон" 
                           required 
                           style="flex: 1; min-width: 200px; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;"
                           id="tour-phone">
                </div>
                
                <div>
                    <textarea 
                        placeholder="Комментарий (например, удобная дата или особые пожелания)"
                        style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; min-height: 80px; resize: vertical;"
                        id="tour-comment"></textarea>
                </div>
                
                <!-- ЧЕКБОКС СОГЛАСИЯ -->
                <div style="margin: 10px 0; padding: 15px; background: white; border-radius: 5px; border: 1px solid #e0e0e0;">
                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 14px; color: #555;">
                        <input type="checkbox" 
                               required 
                               style="margin-top: 3px; accent-color: #F77C2A; min-width: 18px; flex-shrink: 0;"
                               id="tour-privacy-checkbox">
                        <span style="flex: 1;">
                            Я согласен(а) на обработку персональных данных в соответствии с 
                            <a href="javascript:void(0);" 
                               onclick="openPrivacyModal(); return false;" 
                               style="color: #F77C2A; text-decoration: underline;">
                                Политикой конфиденциальности
                            </a>
                        </span>
                    </label>
                </div>
                
                <button type="submit" 
                        style="padding: 14px 30px; background: #F77C2A; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; align-self: flex-start;">
                    ОТПРАВИТЬ ЗАЯВКУ
                </button>
            </form>
            
            <div style="margin-top: 15px; font-size: 12px; color: #666; line-height: 1.4;">
                <p>📞 Мы перезвоним вам в течение часа, чтобы согласовать дату и время экскурсии.</p>
                <p>📍 Адрес цеха: Дорожный переулок, 5
посёлок Индустриальный, городской округ Краснодар
</p>
            </div>
        </div>
    `
};

// === ОСНОВНЫЕ ФУНКЦИИ ===
// 2. Функция для перехода к расчету молочного оборудования
function scrollToDairyCalculation() {
    const calculationSection = document.getElementById('calculation');
    if (calculationSection) {
        calculationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => {
            activateTab('dairy');
        }, 300);
    }
}

// 3. Функция для перехода к расчету винодельческого оборудования
function scrollToWineryCalculation() {
    const calculationSection = document.getElementById('calculation');
    if (calculationSection) {
        calculationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => {
            activateTab('winery');
        }, 300);
    }
}

// 4. Функция активации вкладок
function activateTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
        tab.style.display = 'none';
    });
    const activeForm = document.getElementById(tabId + '-form');
    if (activeForm) {
        activeForm.style.display = 'block';
        activeForm.classList.add('active');
    }
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const activeBtn = document.querySelector('.tab-btn[data-tab="' + tabId + '"]');
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}

// 5. Функция загрузки данных лота пивоварни
function loadBreweryLotData(lotId) {
    const lotData = breweryLotsData[lotId];
    if (!lotData) return;
    document.getElementById('lot-volume').textContent = lotData.volume;
    document.getElementById('lot-production').textContent = lotData.production;
    document.getElementById('lot-term').textContent = lotData.term;
    fillEquipmentCategoryWithCheckboxes('brewing-equipment', lotData.brewing);
    fillEquipmentCategoryWithCheckboxes('auxiliary-equipment', lotData.auxiliary);
    fillEquipmentCategoryWithCheckboxes('automation-equipment', lotData.automation);
    fillFermentationCategoryWithCheckboxes(lotData);
    addCheckboxListeners();
}

// 6. Функция заполнения категории с чекбоксами
function fillEquipmentCategoryWithCheckboxes(containerId, items) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';
    items.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'equipment-item checkbox-item';
        itemElement.innerHTML = `
            <label class="equipment-checkbox-label">
                <input type="checkbox" class="equipment-checkbox-input" checked 
                       data-equipment="${item}">
                <span class="custom-checkbox"></span>
                <div class="equipment-details">
                    <p>${item}</p>
                </div>
            </label>
        `;
        container.appendChild(itemElement);
    });
}

// 7. Функция заполнения ферментационного цеха
function fillFermentationCategoryWithCheckboxes(lotData) {
    const container = document.getElementById('fermentation-equipment');
    if (!container) return;
    container.innerHTML = '';
    for (let i = 1; i <= 3; i++) {
        const cktElement = document.createElement('div');
        cktElement.className = 'equipment-item checkbox-item ckt-item';
        cktElement.innerHTML = `
            <label class="equipment-checkbox-label">
                <input type="checkbox" class="equipment-checkbox-input" checked 
                       data-equipment="ЦКТ ${lotData.ckt.volume}л"
                       data-volume="${lotData.ckt.volume}">
                <span class="custom-checkbox"></span>
                <div class="equipment-details">
                    <h5>ЦКТ ${i}</h5>
                    <div class="equipment-specs">
                        <div class="spec-item">
                            <span>Объем: ${lotData.ckt.volume} л</span>
                        </div>
                        <div class="spec-item">
                            <span>Охлаждение: 2 рубашки</span>
                        </div>
                    </div>
                </div>
            </label>
        `;
        container.appendChild(cktElement);
    }
    lotData.fermentation.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'equipment-item checkbox-item';
        itemElement.innerHTML = `
            <label class="equipment-checkbox-label">
                <input type="checkbox" class="equipment-checkbox-input" checked 
                       data-equipment="${item}">
                <span class="custom-checkbox"></span>
                <div class="equipment-details">
                    <p>${item}</p>
                </div>
            </label>
        `;
        container.appendChild(itemElement);
    });
}

// 8. Функция добавления обработчиков чекбоксов
function addCheckboxListeners() {
    document.querySelectorAll('.equipment-checkbox-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const parentItem = this.closest('.equipment-item');
            if (this.checked) {
                parentItem.classList.remove('unselected');
            } else {
                parentItem.classList.add('unselected');
            }
            updateSelectedSummary();
        });
    });
}

// 9. Функция для отображения выбранных элементов
function updateSelectedSummary() {
    const selectedContainer = document.getElementById('selected-items-container');
    const itemsCountElement = document.getElementById('items-count');
    if (!selectedContainer) return;
    const selectedItems = document.querySelectorAll('.equipment-checkbox-input:checked');
    const itemsMap = new Map();
    selectedItems.forEach(item => {
        const equipmentName = item.getAttribute('data-equipment');
        if (itemsMap.has(equipmentName)) {
            itemsMap.set(equipmentName, itemsMap.get(equipmentName) + 1);
        } else {
            itemsMap.set(equipmentName, 1);
        }
    });
    let html = '';
    itemsMap.forEach((count, equipmentName) => {
        const quantity = count > 1 ? ` (${count} шт.)` : '';
        html += `<div class="selected-item"><span class="selected-item-name">${equipmentName}${quantity}</span></div>`;
    });
    if (itemsMap.size > 0) {
        selectedContainer.innerHTML = html;
    } else {
        selectedContainer.innerHTML = '<div class="empty-selection">Выберите оборудование из левых колонок</div>';
    }
    itemsCountElement.textContent = `${itemsMap.size} позиций`;
}

// 10. Функция добавления дополнительного ЦКТ
function addAdditionalCKT() {
    const container = document.getElementById('fermentation-equipment');
    const cktElement = document.createElement('div');
    cktElement.className = 'equipment-item checkbox-item additional-ckt';
    cktElement.innerHTML = `
        <label class="equipment-checkbox-label">
            <input type="checkbox" class="equipment-checkbox-input" checked 
                   data-equipment="Дополнительный ЦКТ">
            <span class="custom-checkbox"></span>
            <div class="equipment-details">
                <h5>Дополнительный ЦКТ</h5>
                <div class="equipment-specs">
                    <div class="spec-item">
                        <div class="volume-select">
                            <select class="ckt-volume-select">
                                <option value="500">500 л</option>
                                <option value="1000">1000 л</option>
                                <option value="2000">2000 л</option>
                                <option value="3000">3000 л</option>
                                <option value="6000">6000 л</option>
                            </select>
                        </div>
                    </div>
                    <div class="spec-item">
                        <span>Охлаждение: 2 рубашки</span>
                    </div>
                </div>
            </div>
        </label>
        <button type="button" class="remove-ckt-btn">×</button>
    `;
    container.appendChild(cktElement);
    cktElement.querySelector('.remove-ckt-btn').addEventListener('click', function() {
        cktElement.remove();
        updateSelectedSummary();
    });
    cktElement.querySelector('.ckt-volume-select').addEventListener('change', function() {
        const checkbox = cktElement.querySelector('.equipment-checkbox-input');
        const selectedVolume = this.value;
        checkbox.setAttribute('data-equipment', `Дополнительный ЦКТ ${selectedVolume}л`);
        updateSelectedSummary();
    });
    cktElement.querySelector('.equipment-checkbox-input').addEventListener('change', function() {
        const parentItem = this.closest('.equipment-item');
        if (this.checked) {
            parentItem.classList.remove('unselected');
        } else {
            parentItem.classList.add('unselected');
        }
        updateSelectedSummary();
    });
    updateSelectedSummary();
}

// ФУНКЦИЯ: ДОБАВИТЬ НОВУЮ ЕМКОСТЬ (ИСПРАВЛЕННАЯ)
function addTankCard() {
    console.log('➕ Добавление новой емкости...');
    
    const template = document.getElementById('detailed-tank-template');
    const container = document.querySelector('#detailed-form .configurator-container');
    
    if (!template || !container) {
        alert('Не удалось добавить емкость. Обновите страницу.');
        return;
    }
    
    // Считаем сколько уже есть емкостей (включая первую)
    const existingTanks = container.querySelectorAll('.tank-configurator.detailed-tank, #tank-1');
    const newTankNumber = existingTanks.length + 1;
    
    console.log(`Создаем емкость #${newTankNumber}`);
    
    // Клонируем шаблон
    const templateContent = template.innerHTML;
    
    // Заменяем все X на номер емкости
    const newTankHTML = templateContent
        .replace(/data-tank-number="X"/g, `data-tank-number="${newTankNumber}"`)
        .replace(/<h4>Емкость X<\/h4>/g, `<h4>Емкость ${newTankNumber}</h4>`)
        .replace(/name="([^"]+)_X"/g, `name="$1_${newTankNumber}"`);
    
    // Создаем элемент из HTML
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = newTankHTML;
    const tankElement = tempDiv.firstElementChild;
    
    if (!tankElement) {
        console.error('Не удалось создать емкость из шаблона');
        return;
    }
    
    // Устанавливаем ID для емкости
    tankElement.id = `tank-${newTankNumber}`;
    
    // Назначаем кнопку удаления
    const removeBtn = tankElement.querySelector('.remove-equipment-btn');
    if (removeBtn) {
        removeBtn.onclick = function() {
            if (confirm('Удалить эту емкость?')) {
                tankElement.remove();
                console.log(`🗑️ Удалена емкость #${newTankNumber}`);
                updateAllTankNumbers();
            }
        };
    }
    
    // Добавляем обработчик для выбора типа
    const typeSelect = tankElement.querySelector('.tank-type-select');
    if (typeSelect) {
        typeSelect.id = `tank-type-${newTankNumber}`;
        
        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            console.log(`Изменен тип емкости #${newTankNumber}:`, selectedType);
            autoSelectOptionsForTank(selectedType, newTankNumber);
        });
    }
    
    // Находим и обновляем все чекбоксы
    tankElement.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        const originalName = checkbox.getAttribute('name');
        if (originalName && originalName.includes('_X')) {
            checkbox.name = originalName.replace('_X', `_${newTankNumber}`);
        }
    });
    
    // Добавляем в контейнер (перед кнопкой добавления)
    const addButton = container.querySelector('.add-tank-btn');
    if (addButton) {
        container.insertBefore(tankElement, addButton);
    } else {
        container.appendChild(tankElement);
    }
    
    // Прокручиваем к новой емкости
    setTimeout(() => {
        tankElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
        
        // Подсвечиваем
        tankElement.style.boxShadow = '0 0 0 3px #F77C2A';
        tankElement.style.transition = 'box-shadow 0.5s ease';
        
        setTimeout(() => {
            tankElement.style.boxShadow = '';
        }, 1500);
    }, 100);
    
    console.log(`✅ Добавлена емкость #${newTankNumber}`, tankElement);
}

// ФУНКЦИЯ: АВТОВЫБОР ОПЦИЙ ДЛЯ КОНКРЕТНОЙ ЕМКОСТИ
function autoSelectOptionsForTank(tankType, tankNumber) {
    console.log(`⚙️ Автовыбор опций для емкости #${tankNumber}:`, tankType);
    
    // Находим карточку емкости
    const tankCard = document.getElementById(`tank-${tankNumber}`);
    if (!tankCard) {
        console.error(`Не найдена емкость #${tankNumber}`);
        return;
    }
    
    // Сначала снимаем все отметки с этой емкости
    tankCard.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Правила для каждого типа
    const typeRules = {
        'storage': [], // для хранения обычно без опций
        'mixing': [`mixer_${tankNumber}`], // только мешалка
        'thermal': [`heating_${tankNumber}`, `cooling_${tankNumber}`], // нагрев + охлаждение
        'pressure': [], // для давления особых опций нет
        'sip': [`sip_${tankNumber}`], // только SIP
        'heat-exchanger': [`heating_${tankNumber}`, `cooling_${tankNumber}`] // и нагрев и охлаждение
    };
    
    // Отмечаем нужные чекбоксы
    const checkboxesToCheck = typeRules[tankType] || [];
    
    checkboxesToCheck.forEach(checkboxName => {
        const checkbox = tankCard.querySelector(`[name="${checkboxName}"]`);
        if (checkbox) {
            checkbox.checked = true;
            console.log(`✅ Отмечен чекбокс: ${checkboxName}`);
        } else {
            console.warn(`❌ Чекбокс не найден: ${checkboxName}`);
        }
    });
    
    // Показываем подсказку
    showTankTypeHintForCard(tankType, tankCard);
}

// ФУНКЦИЯ: ОБНОВИТЬ ВСЕ НОМЕРА ЕМКОСТЕЙ
function updateAllTankNumbers() {
    console.log('🔄 Обновление нумерации всех емкостей...');
    
    const container = document.querySelector('#detailed-form .configurator-container');
    if (!container) return;
    
    // Находим все емкости (включая первую)
    const allTanks = container.querySelectorAll('.tank-configurator.detailed-tank, #tank-1');
    
    allTanks.forEach((tank, index) => {
        const newTankNumber = index + 1;
        
        // Обновляем атрибут data-tank-number
        tank.setAttribute('data-tank-number', newTankNumber);
        
        // Обновляем ID емкости
        if (newTankNumber === 1) {
            tank.id = 'tank-1';
        } else {
            tank.id = `tank-${newTankNumber}`;
        }
        
        // Обновляем заголовок
        const title = tank.querySelector('h4');
        if (title) title.textContent = `Емкость ${newTankNumber}`;
        
        // Обновляем имена всех полей внутри этой емкости
        tank.querySelectorAll('[name]').forEach(input => {
            const currentName = input.getAttribute('name');
            
            // Находим номер в текущем имени
            const match = currentName.match(/_(\d+)$/);
            if (match) {
                const currentNumber = match[1];
                const newName = currentName.replace(`_${currentNumber}`, `_${newTankNumber}`);
                input.name = newName;
                
                // Обновляем ID для select типа
                if (input.classList.contains('tank-type-select')) {
                    input.id = `tank-type-${newTankNumber}`;
                }
            }
        });
    });
    
    console.log(`✅ Обновлена нумерация: ${allTanks.length} емкостей`);
}

// ФУНКЦИЯ: ИНИЦИАЛИЗИРОВАТЬ СУЩЕСТВУЮЩИЕ ЕМКОСТИ
function initializeExistingTanks() {
    console.log('🔧 Инициализация существующих емкостей...');
    
    // Находим первую емкость (она уже есть в HTML)
    const firstTank = document.getElementById('tank-1');
    if (firstTank) {
        // Добавляем обработчик для типа
        const typeSelect = firstTank.querySelector('.tank-type-select');
        if (typeSelect) {
            typeSelect.id = 'tank-type-1';
            
            typeSelect.addEventListener('change', function() {
                autoSelectOptionsForTank(this.value, 1);
            });
            
            // Если уже выбран тип, применяем опции
            if (typeSelect.value) {
                autoSelectOptionsForTank(typeSelect.value, 1);
            }
        }
        
        // Добавляем кнопку удаления (только для не первой емкости)
        // Для первой емкости кнопки удаления нет
    }
}

// ФУНКЦИЯ: ПЕРЕХОД К ФОРМЕ С ВЫБРАННЫМ ТИПОМ ЕМКОСТИ
function scrollToTankType(tankType) {
    console.log('🔄 Переход к расчету для типа:', tankType);
    
    // Прокручиваем к форме расчета
    const calculationSection = document.getElementById('calculation');
    if (calculationSection) {
        calculationSection.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }
    
    // Через 0.5 секунды переключаем на вкладку "Подробный расчет"
    setTimeout(function() {
        const detailedTabBtn = document.querySelector('.tab-btn[data-tab="detailed"]');
        const detailedForm = document.getElementById('detailed-form');
        
        if (detailedTabBtn && detailedForm) {
            // Активируем вкладку "Подробный расчет"
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            detailedTabBtn.classList.add('active');
            detailedForm.classList.add('active');
            
            // Устанавливаем тип в первой емкости
            setTimeout(function() {
                const firstTypeSelect = document.getElementById('tank-type-1');
                if (firstTypeSelect) {
                    firstTypeSelect.value = tankType;
                    
                    // Запускаем обработчик изменения
                    const event = new Event('change');
                    firstTypeSelect.dispatchEvent(event);
                    
                    console.log('✅ Установлен тип для первой емкости:', tankType);
                }
            }, 300);
        }
    }, 500);
}

// ФУНКЦИЯ: ВЫБРАТЬ ТИП ДЛЯ КОНКРЕТНОЙ ЕМКОСТИ
function selectTankTypeInForm(tankType, tankNumber = 1) {
    console.log(`🎯 Устанавливаем тип емкости #${tankNumber}:`, tankType);
    
    // Находим контейнер нужной емкости
    const tankContainer = document.querySelector(`#detailed-form .tank-configurator:nth-of-type(${tankNumber})`);
    if (!tankContainer) {
        console.error(`❌ Не найдена емкость #${tankNumber}`);
        return;
    }
    
    // Соответствие названий
    const typeMapping = {
        'storage': 'storage',
        'mixing': 'mixing',
        'thermal': 'thermal',
        'pressure': 'pressure',
        'sip': 'sip',
        'heat-exchanger': 'heat-exchanger'
    };
    
    // Автоматически отмечаем типичные опции для этого типа
    autoSelectOptionsForType(tankType, tankNumber);
}

// ФУНКЦИЯ: ПОКАЗАТЬ ПОДСКАЗКУ ДЛЯ КАРТОЧКИ
function showTankTypeHintForCard(tankType, tankCard) {
    // Удаляем старую подсказку если есть
    const oldHint = tankCard.querySelector('.tank-type-hint');
    if (oldHint) oldHint.remove();
    
    // Тексты подсказок
    const hints = {
        'storage': 'Для емкости хранения вы можете добавить насос для перекачки',
        'mixing': 'Для емкости с мешалкой выберите скорость вращения в комментариях',
        'thermal': 'Укажите нужный температурный диапазон в комментариях',
        'pressure': 'Укажите рабочее давление (до 6 бар)',
        'sip': 'Укажите требования к стерилизации',
        'heat-exchanger': 'Укажите производительность теплообменника'
    };
    
    const hintText = hints[tankType];
    if (!hintText) return;
    
    // Создаем элемент подсказки
    const hint = document.createElement('div');
    hint.className = 'tank-type-hint';
    hint.innerHTML = `
        <div style="
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #2c3e50;
        ">
            <strong>💡 Подсказка:</strong> ${hintText}
        </div>
    `;
    
    // Вставляем после выпадающего списка типа
    const typeSelect = tankCard.querySelector('.tank-type-select');
    if (typeSelect && typeSelect.parentNode) {
        typeSelect.parentNode.insertBefore(hint, typeSelect.nextSibling);
    }
    
    // Убираем через 10 секунд
    setTimeout(() => {
        if (hint.parentNode) {
            hint.style.opacity = '0';
            hint.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                if (hint.parentNode) hint.parentNode.removeChild(hint);
            }, 500);
        }
    }, 10000);
}

// 12. Функция настройки селектора молочного оборудования
function setupDairyEquipmentSelect() {
    const select = document.getElementById('dairy-equipment-select');
    const volumeInput = document.querySelector('.dairy-volume-input');
    if (select && volumeInput) {
        select.addEventListener('change', function() {
            const equipmentType = this.value;
            const equipmentData = dairyEquipmentData[equipmentType];
            if (equipmentData && equipmentData.defaultVolume !== null) {
                volumeInput.value = equipmentData.defaultVolume;
                volumeInput.placeholder = `Рекомендуемый объем: ${equipmentData.defaultVolume} л`;
            } else {
                volumeInput.value = '';
                volumeInput.placeholder = 'Укажите необходимый объем';
            }
            const options = document.querySelectorAll('.dairy-option');
            options.forEach(option => option.checked = false);
            if (equipmentType === 'cooling' || equipmentType === 'pasteurizer') {
                const coolingOption = document.querySelector('.dairy-option[data-option="cooling"]');
                if (coolingOption) coolingOption.checked = true;
            }
            if (equipmentType === 'cheese-maker' || equipmentType === 'curd-maker') {
                const mixingOption = document.querySelector('.dairy-option[data-option="mixing"]');
                const heatingOption = document.querySelector('.dairy-option[data-option="heating"]');
                if (mixingOption) mixingOption.checked = true;
                if (heatingOption) heatingOption.checked = true;
            }
            if (equipmentType === 'processing' || equipmentType === 'fermentation') {
                const cipOption = document.querySelector('.dairy-option[data-option="cip"]');
                const automationOption = document.querySelector('.dairy-option[data-option="automation"]');
                if (cipOption) cipOption.checked = true;
                if (automationOption) automationOption.checked = true;
            }
        });
    }
}

// 13. Функция настройки селектора винодельческого оборудования
function setupWineryEquipmentSelect() {
    const select = document.getElementById('winery-equipment-select');
    const volumeInput = document.querySelector('.winery-volume-input');
    if (select && volumeInput) {
        select.addEventListener('change', function() {
            const equipmentType = this.value;
            const equipmentData = wineryEquipmentData[equipmentType];
            if (equipmentData && equipmentData.defaultVolume !== null) {
                volumeInput.value = equipmentData.defaultVolume;
                volumeInput.placeholder = `Рекомендуемый объем: ${equipmentData.defaultVolume} л`;
            } else {
                volumeInput.value = '';
                volumeInput.placeholder = 'Укажите необходимый объем';
            }
            const options = document.querySelectorAll('.winery-option');
            options.forEach(option => option.checked = false);
            
            if (equipmentType === 'fermentation' || equipmentType === 'storage' || equipmentType === 'blending') {
                const cipOption = document.querySelector('.winery-option[data-option="cip"]');
                const inertizationOption = document.querySelector('.winery-option[data-option="inertization"]');
                if (cipOption) cipOption.checked = true;
                if (inertizationOption) inertizationOption.checked = true;
            }
            if (equipmentType === 'clarification' || equipmentType === 'filtration') {
                const coolingOption = document.querySelector('.winery-option[data-option="cooling"]');
                const cipOption = document.querySelector('.winery-option[data-option="cip"]');
                if (coolingOption) coolingOption.checked = true;
                if (cipOption) cipOption.checked = true;
            }
            if (equipmentType === 'thermal' || equipmentType === 'pasteurizer') {
                const heatingOption = document.querySelector('.winery-option[data-option="heating"]');
                const coolingOption = document.querySelector('.winery-option[data-option="cooling"]');
                if (heatingOption) heatingOption.checked = true;
                if (coolingOption) coolingOption.checked = true;
            }
            if (equipmentType === 'sulfitation' || equipmentType === 'blending') {
                const mixingOption = document.querySelector('.winery-option[data-option="mixing"]');
                if (mixingOption) mixingOption.checked = true;
            }
        });
    }
}

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

// === ФУНКЦИИ ДЛЯ МОДАЛЬНЫХ ОКОН ===
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
    
    const privacyCheckbox = document.getElementById('tour-privacy-checkbox');
    if (!privacyCheckbox.checked) {
        alert('Пожалуйста, дайте согласие на обработку персональных данных');
        privacyCheckbox.focus();
        return;
    }
    
    const name = document.getElementById('tour-name');
    const phone = document.getElementById('tour-phone');
    
    if (!name.value || !phone.value) {
        alert('Пожалуйста, заполните имя и телефон');
        return;
    }
    
    alert(`✅ Заявка на экскурсию отправлена!\n\nМы перезвоним вам по номеру ${phone.value} в течение часа для согласования даты и времени.`);
    
    // Очищаем форму
    document.getElementById('tour-request-form').reset();
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


// 30. Функция закрытия окна предпросмотра
function closePreviewModal() {
    const modal = document.getElementById('certificate-preview-modal');
    if (modal) {
        modal.style.display = 'none';
    }
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
    // Для основных проектов (1-5)
    if (projectId.startsWith('project-')) {
        const num = projectId.replace('project-', '');
        const modal = document.getElementById(`project-modal-${num}`);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    // Для пивоваренных проектов
    else if (projectId.startsWith('brewery-')) {
        const num = projectId.replace('brewery-', '');
        const modal = document.getElementById(`brewery-modal-${num}`);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
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
// // Эта функция заменяет проблемный код, который мешал отправке данных левой колонки
function setupDetailedFormValidation() {
    const form = document.querySelector('#detailed-form form');
    if (!form) return;
    
    // Убираем ВСЕ существующие обработчики submit
    form.onsubmit = null;
    
    // Добавляем новый обработчик, который проверяет только обязательные поля
    form.addEventListener('submit', function(e) {
        console.log('📤 Отправка формы подробного расчета...');
        
        // Проверяем обязательные поля
        const name = this.querySelector('[name="name"]');
        const phone = this.querySelector('[name="phone"]');
        const checkbox = this.querySelector('[name="agreement"]');
        
        let hasError = false;
        
        if (!name || !name.value.trim()) {
            alert('Введите имя');
            name.focus();
            hasError = true;
        }
        
        if (!hasError && (!phone || !phone.value.trim())) {
            alert('Введите телефон');
            phone.focus();
            hasError = true;
        }
        
        if (!hasError && (!checkbox || !checkbox.checked)) {
            alert('Дайте согласие на обработку данных');
            checkbox.focus();
            hasError = true;
        }
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
        
        // Если все ок - форма отправляется как обычно
        console.log('✅ Все проверки пройдены, форма отправляется...');
        return true;
    });
}

// === ОСНОВНОЙ КОД ===
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
    } else {
        console.error("Не удалось найти кнопку или секцию Хранилища");
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
                loadBreweryLotData(this.value);
                updateSelectedSummary();
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
            addAdditionalCKT();
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
            scrollToWineryCalculation();
        });
    }
    
    // === ПЕРЕКЛЮЧЕНИЕ ВКЛАДОК ФОРМЫ РАСЧЕТА ===
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            console.log("Переключение на вкладку:", tabId);
            activateTab(tabId);
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
            scrollToDairyCalculation();
        });
    }
    
    const dairyAddBtn = document.querySelector('.dairy-add-equipment-btn');
    if (dairyAddBtn) {
        dairyAddBtn.addEventListener('click', function() {
            console.log('Добавление молочного оборудования');
            // Здесь должна быть функция добавления молочного оборудования
        });
    }
    
    const dairyTabBtn = document.querySelector('.tab-btn[data-tab="dairy"]');
    if (dairyTabBtn) {
        dairyTabBtn.addEventListener('click', function() {
            activateTab('dairy');
        });
    }
    
    // === ОБРАБОТЧИКИ ДЛЯ ВИНОДЕЛЬЧЕСКОГО ОБОРУДОВАНИЯ ===
    const wineryCalculateBtn = document.querySelector('.winery-calculate-btn');
    if (wineryCalculateBtn) {
        wineryCalculateBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation();
            console.log('Переход к расчету винодельческого оборудования');
            scrollToWineryCalculation();
        });
    }
    
    const wineryAddBtn = document.querySelector('.winery-add-equipment-btn');
    if (wineryAddBtn) {
        wineryAddBtn.addEventListener('click', function() {
            console.log('Добавление винодельческого оборудования');
            // Здесь должна быть функция добавления винодельческого оборудования
        });
    }
    
    const wineryTabBtn = document.querySelector('.tab-btn[data-tab="winery"]');
    if (wineryTabBtn) {
        wineryTabBtn.addEventListener('click', function() {
            activateTab('winery');
        });
    }
    
    // Инициализация молочной формы
    setTimeout(() => {
        setupDairyEquipmentSelect();
    }, 500);
    
    // Инициализация винодельческой формы
    setTimeout(() => {
        setupWineryEquipmentSelect();
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
        setupBreweryForm();
        setupWineryForm();
    }, 500);

    
    // Или с задержкой для надежности:
    setTimeout(initYandexMap, 1000);
    
    console.log("=== ЗАВЕРШЕНИЕ ИНИЦИАЛИЗАЦИИ ===");
});

// Пивоваренные проекты
    for (let i = 1; i <= 3; i++) {
        const modal = document.getElementById(`brewery-modal-${i}`);
        if (modal && modal.style.display === 'flex' && e.target.classList.contains('project-modal-overlay')) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }


// Экспортируем ВСЕ функции в глобальную область видимости
window.scrollToCalculationForm = scrollToCalculationForm;
window.scrollToDairyCalculation = scrollToDairyCalculation;
window.scrollToWineryCalculation = scrollToWineryCalculation;
window.activateTab = activateTab;
window.loadBreweryLotData = loadBreweryLotData;
window.addAdditionalCKT = addAdditionalCKT;
window.addTankCard = addTankCard;
window.updateSelectedSummary = updateSelectedSummary;

window.initYandexMap = initYandexMap;

window.openTourModal = openTourModal;
window.closeTourModal = closeTourModal;
window.submitTourRequest = submitTourRequest;
window.openPrivacyModal = openPrivacyModal;
window.closePrivacyModal = closePrivacyModal;
window.openCertificatesModal = openCertificatesModal;
window.closeCertificatesModal = closeCertificatesModal;
window.downloadCertificate = downloadCertificate;
window.downloadAllCertificates = downloadAllCertificates;


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
window.closeProjectModal = closeProjectModal;
window.orderSimilarProject = orderSimilarProject;
window.scrollToEquipmentExamples = scrollToEquipmentExamples;
window.scrollToContacts = scrollToContacts;

window.scrollToCategories = scrollToCategories;
window.scrollToAutomationForm = scrollToAutomationForm;
window.fixAllPrivacyLinks = fixAllPrivacyLinks;
window.fixCertificatesButton = fixCertificatesButton;
window.fixTourButton = fixTourButton;

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

// Экспортируем в глобальную область
window.scrollToAutomationForm = scrollToAutomationForm;
// ПРОСТАЯ ФУНКЦИЯ ДЛЯ КНОПОК
// 1. ФУНКЦИЯ ДЛЯ КНОПОК КАТЕГОРИЙ ОБОРУДОВАНИЯ
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

// Экспортируем
window.showCategory = showCategory;
function scrollToBreweryLot(lotNumber, event = null) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    console.log('🚀 ЗАПУСК ЛОТА:', lotNumber);
    console.log('Данные лота:', breweryLotsData[lotNumber]);
    
    // Быстрая проверка данных
    if (!breweryLotsData[lotNumber]) {
        console.error('❌ НЕТ ДАННЫХ! Доступные лоты:', Object.keys(breweryLotsData));
        return;
    }
    
    const calculationSection = document.getElementById('calculation');
    if (!calculationSection) return;
    
    calculationSection.scrollIntoView({ behavior: 'smooth' });
    
    setTimeout(() => {
        const breweryTab = document.querySelector('.tab-btn[data-tab="brewery"]');
        if (!breweryTab) return;
        
        breweryTab.click();
        
        setTimeout(() => {
            const lotSelect = document.getElementById('brewery-lot-select');
            if (lotSelect) {
                console.log('Выбираем лот в селекторе:', lotNumber);
                lotSelect.value = lotNumber;
                
                // Создаем и отправляем событие
                const changeEvent = new Event('change', {
                    bubbles: true,
                    cancelable: true
                });
                console.log('Отправляем событие change...');
                lotSelect.dispatchEvent(changeEvent);
                
                // ВРУЧНУЮ вызываем загрузку (на всякий случай)
                setTimeout(() => {
                    console.log('Вручную вызываем loadBreweryLotData...');
                    if (typeof loadBreweryLotData === 'function') {
                        loadBreweryLotData(lotNumber);
                    }
                    
                    // Проверяем загрузилось ли
                    setTimeout(() => {
                        console.log('Проверяем загрузку...');
                        const volumeElement = document.getElementById('lot-volume');
                        console.log('Объем лота:', volumeElement?.textContent);
                    }, 300);
                }, 200);
            }
        }, 600);
    }, 400);
}

// Экспортируем
window.scrollToBreweryLot = scrollToBreweryLot;
// Экспортируем
window.scrollToBreweryLot = scrollToBreweryLot;


// === ФУНКЦИЯ ДЛЯ ПОДГОТОВКИ ДАННЫХ ПИВОВАРНИ К ОТПРАВКЕ ===
function prepareBreweryData() {
    console.log('📦 Подготавливаем данные пивоварни для отправки...');
    
    // 1. Получаем выбранный лот
    const lotSelect = document.getElementById('brewery-lot-select');
    let lotInfo = '';
    
    if (lotSelect && lotSelect.value) {
        const lotData = breweryLotsData[lotSelect.value];
        if (lotData) {
            lotInfo = `Лот: ${lotData.name}\n`;
            lotInfo += `Объем варки: ${lotData.volume}\n`;
            lotInfo += `Производительность: ${lotData.production}\n`;
            lotInfo += `Срок изготовления: ${lotData.term}\n`;
        }
    }
    
    // 2. Собираем ВСЕ выбранное оборудование
    let equipmentList = '';
    let itemsCount = 0;
    
    document.querySelectorAll('.equipment-checkbox-input:checked').forEach((checkbox, index) => {
        const itemName = checkbox.getAttribute('data-equipment');
        if (itemName) {
            equipmentList += `${index + 1}. ${itemName}\n`;
            itemsCount++;
        }
    });
    
    // 3. Добавляем дополнительное оборудование
    document.querySelectorAll('.additional-ckt').forEach(ckt => {
        const volumeSelect = ckt.querySelector('.ckt-volume-select');
        if (volumeSelect) {
            itemsCount++;
            equipmentList += `${itemsCount}. Доп. ЦКТ ${volumeSelect.value}л\n`;
        }
    });
    
    // 4. Собираем всё в один текст
    let breweryData = '';
    
    if (lotInfo) {
        breweryData += '=== ВЫБРАННЫЙ ЛОТ ПИВОВАРНИ ===\n';
        breweryData += lotInfo + '\n';
    }
    
    if (equipmentList) {
        breweryData += '=== ВЫБРАННОЕ ОБОРУДОВАНИЕ ===\n';
        breweryData += equipmentList + '\n';
    }
    
    // 5. Предварительная стоимость
    const totalPriceElement = document.getElementById('total-price');
    if (totalPriceElement && totalPriceElement.textContent !== '0') {
        breweryData += `\nПредварительная стоимость: ${totalPriceElement.textContent} руб.\n`;
    }
    
    return breweryData;
}

// === ИСПРАВЛЕННАЯ ФУНКЦИЯ ОТПРАВКИ (ВАЖНО!) ===
function handleBrewerySubmit(event) {
    console.log('🚀 Начинаем отправку пивоварни...');
    
    // 1. ПРЕДОТВРАЩАЕМ стандартную отправку формы
    event.preventDefault();
    event.stopPropagation();
    
    // 2. Проверяем обязательные поля
    const nameField = document.getElementById('brewery-name');
    const phoneField = document.getElementById('brewery-phone');
    const privacyCheckbox = document.getElementById('brewery-privacy-checkbox');
    
    if (!nameField || !nameField.value.trim()) {
        alert('Введите имя');
        nameField?.focus();
        return false;
    }
    
    if (!phoneField || !phoneField.value.trim()) {
        alert('Введите телефон');
        phoneField?.focus();
        return false;
    }
    
    if (!privacyCheckbox || !privacyCheckbox.checked) {
        alert('Примите политику конфиденциальности');
        privacyCheckbox?.focus();
        return false;
    }
    
    // 3. Собираем данные пивоварни
    const breweryData = prepareBreweryData();
    console.log('Данные пивоварни:', breweryData);
    
    // 4. Создаем СКРЫТУЮ ФОРМУ для отправки
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'send.php';
    form.style.display = 'none';
    
    // 5. Добавляем обязательные поля (вместе с данными пивоварни)
    const fields = {
        'form_type': 'brewery',
        'name': nameField.value.trim(),
        'phone': phoneField.value.trim(),
        'selected_equipment': breweryData
    };
    
    // Email (необязательное)
    const emailField = document.getElementById('brewery-email');
    if (emailField && emailField.value.trim()) {
        fields['email'] = emailField.value.trim();
    }
    
    // Комментарий пользователя
    const commentField = document.getElementById('brewery-comment');
    if (commentField && commentField.value.trim()) {
        fields['message'] = commentField.value.trim() + '\n\n' + breweryData;
    } else {
        fields['message'] = breweryData;
    }
    
    // 6. Добавляем все поля в форму
    Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });
    
    // 7. Добавляем файл (если есть)
    const fileInput = document.getElementById('brewery-file');
    if (fileInput && fileInput.files.length > 0) {
        // Копируем файл в форму
        const newFileInput = document.createElement('input');
        newFileInput.type = 'file';
        newFileInput.name = 'attachment';
        
        // Создаем DataTransfer для копирования файла
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(fileInput.files[0]);
        newFileInput.files = dataTransfer.files;
        
        form.appendChild(newFileInput);
        console.log('Файл добавлен:', fileInput.files[0].name);
    }
    
    // 8. Добавляем форму на страницу
    document.body.appendChild(form);
    console.log('Отправляем форму с данными:', fields);
    
    // 9. Показываем БЫСТРОЕ уведомление (без подтверждения)
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: #27ae60;
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
            <span style="font-size: 24px;">✅</span>
            <div>
                <strong style="display: block;">Заявка отправляется...</strong>
                <small>Перенаправляем на страницу подтверждения</small>
            </div>
        </div>
    `;
    
    // Добавляем стили для анимации
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    document.body.appendChild(notification);
    
    // 10. Через 1.5 секунды отправляем форму и удаляем уведомление
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease forwards';
        
        setTimeout(() => {
            notification.remove();
            // ОТПРАВЛЯЕМ ФОРМУ АВТОМАТИЧЕСКИ
            console.log('🔄 Автоматическая отправка формы...');
            form.submit();
        }, 300);
    }, 1500);
    
    // 11. Через 3 секунды удаляем форму
    setTimeout(() => {
        if (form.parentNode) {
            form.parentNode.removeChild(form);
        }
    }, 3000);
    
    return false;
}

// === ФИКС: ПРАВИЛЬНОЕ ПОДКЛЮЧЕНИЕ ФУНКЦИИ К КНОПКЕ ===
function setupBreweryForm() {
    console.log('🔧 Настраиваем форму пивоварни...');
    
    const submitBtn = document.querySelector('#brewery-form .submit-btn');
    if (submitBtn) {
        // Убираем старый обработчик
        submitBtn.onclick = null;
        
        // Добавляем новый
        submitBtn.addEventListener('click', function(e) {
            console.log('🎯 Клик по кнопке отправки пивоварни');
            handleBrewerySubmit(e);
        });
    }
    
    // Также делаем обработчик для всей формы
    const form = document.querySelector('#brewery-form form');
    if (form) {
        form.onsubmit = function(e) {
            console.log('📋 Submit формы пивоварни');
            return handleBrewerySubmit(e);
        };
    }
}

// Запускаем настройку при загрузке
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(setupBreweryForm, 100);
});

// === ПРОСТАЯ ФУНКЦИЯ ДЛЯ ВИНОДЕЛЬЧЕСКОЙ ФОРМЫ ===
function setupWineryForm() {
    console.log('🍷 Настраиваем винодельческую форму...');
    
    const form = document.querySelector('#winery-form form');
    const fileInput = document.getElementById('winery-file');
    const fileName = document.getElementById('winery-file-name');
    
    // Обработка файлов
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                fileName.textContent = `✅ ${file.name}`;
            } else {
                fileName.textContent = 'Файл не выбран';
            }
        });
    }
    
    // Простая валидация перед отправкой
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameField = document.getElementById('winery-name');
            const phoneField = document.getElementById('winery-phone');
            const checkbox = document.getElementById('winery-privacy-checkbox');
            
            if (!nameField || !nameField.value.trim()) {
                alert('Введите имя');
                e.preventDefault();
                nameField.focus();
                return false;
            }
            
            if (!phoneField || !phoneField.value.trim()) {
                alert('Введите телефон');
                e.preventDefault();
                phoneField.focus();
                return false;
            }
            
            if (!checkbox || !checkbox.checked) {
                alert('Примите политику конфиденциальности');
                e.preventDefault();
                checkbox.focus();
                return false;
            }
            
            console.log('🍷 Форма винодельни отправляется...');
            
            // Показываем уведомление
            showNotification('Винодельческое оборудование', '🍷');
            
            // Форма отправится стандартным способом
            return true;
        });
    }
    
    
    // Инициализируем удаление оборудования
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-equipment-btn')) {
            e.target.closest('.tank-configurator').remove();
        }
    });
}

// === УВЕДОМЛЕНИЕ ===
function showNotification(type, emoji) {
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: #8e44ad;
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
            <span style="font-size: 24px;">${emoji}</span>
            <div>
                <strong style="display: block;">Отправка ${type}...</strong>
                <small>Перенаправление на подтверждение</small>
            </div>
        </div>
    `;
    
    // Добавляем стили для анимации
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Удаляем через 3 секунды
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 1500);
}

// === ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ ===
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(setupWineryForm, 100);
});

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

// Назначаем обработчики на все кнопки "Игра" при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Находим все кнопки с игрой
    const gameButtons = [
        document.querySelector('.automation-btn-secondary'), // Кнопка в блоке автоматизации
        document.querySelector('.brewery-game-btn'),        // Кнопка в блоках пивоварни
        document.querySelector('.footer-link[href="#"]')   // Ссылка в футере (нужно уточнить селектор)
    ];
    
    // Назначаем обработчики
    gameButtons.forEach(btn => {
        if (btn) {
            btn.onclick = function(e) {
                e.preventDefault();
                openGameModal();
            };
        }
    });
});

// ФУНКЦИЯ ДЛЯ ВИНОДЕЛЬЧЕСКОЙ ФОРМЫ
function handleWineryTypeChange(selectElement) {
    console.log('Выбран тип винодельческого оборудования:', selectElement.value);
    
    try {
        // Находим родительскую карточку
        const card = selectElement.closest('.tank-configurator');
        if (!card) {
            console.error('Не найден контейнер оборудования');
            return;
        }
        
        // Данные для каждого типа
        const typeConfigs = {
            'fermentation': {
                volume: 5000,
                material: 'aisi316',
                checkboxes: ['heating', 'cooling', 'mixing', 'cip', 'inertization'],
                comment: 'Система контроля температуры и CO₂'
            },
            'storage': {
                volume: 10000,
                material: 'aisi304',
                checkboxes: ['inertization', 'insulation'],
                comment: 'Дубовые вставки (опционально)'
            },
            'sulfitation': {
                volume: 2000,
                material: 'aisi316',
                checkboxes: ['mixing', 'cip'],
                comment: 'Система дозирования SO₂'
            },
            'clarification': {
                volume: 3000,
                material: 'aisi316',
                checkboxes: ['cooling', 'cip'],
                comment: 'Температурный контроль 4-10°C'
            },
            'filtration': {
                volume: 0, // 0 = скрыть поле объема
                material: 'aisi316',
                checkboxes: ['cip', 'automation'],
                comment: 'Мембранные/кизельгуровые фильтры'
            },
            'blending': {
                volume: 5000,
                material: 'aisi304',
                checkboxes: ['mixing', 'cip'],
                comment: 'Точное дозирование компонентов'
            },
            'pasteurizer': {
                volume: 2000,
                material: 'aisi316',
                checkboxes: ['heating', 'cooling'],
                comment: 'Температурный контроль 60-85°C'
            },
            'thermal': {
                volume: 4000,
                material: 'aisi316',
                checkboxes: ['heating', 'cooling', 'insulation'],
                comment: 'PID-регулятор температуры'
            }
        };
        
        const selectedType = selectElement.value;
        const config = typeConfigs[selectedType];
        
        if (!config) {
            console.log('Тип не выбран или не найден');
            return;
        }
        
        // 1. Заполняем скрытое поле с названием типа
        const typeNameInput = card.querySelector('.winery-type-name');
        if (typeNameInput) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            typeNameInput.value = selectedOption.text;
            console.log('Название типа сохранено:', typeNameInput.value);
        }
        
        // 2. Заполняем объем
        const volumeInput = card.querySelector('.winery-volume-input');
        if (volumeInput) {
            if (config.volume === 0) {
                // Для фильтрации
                volumeInput.value = '';
                volumeInput.placeholder = 'Не требуется';
                volumeInput.style.opacity = '0.5';
                volumeInput.removeAttribute('required');
            } else {
                volumeInput.value = config.volume;
                volumeInput.placeholder = 'от 100 до 50 000 литров';
                volumeInput.style.opacity = '1';
                volumeInput.setAttribute('required', 'required');
            }
        }
        
        // 3. Устанавливаем материал
        const materialSelect = card.querySelector('.winery-material-select');
        if (materialSelect) {
            materialSelect.value = config.material;
        }
        
        // 4. Сначала снимаем все отметки
        card.querySelectorAll('.winery-option').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // 5. Отмечаем нужные чекбоксы
        config.checkboxes.forEach(optionName => {
            const checkbox = card.querySelector(`.winery-option[data-option="${optionName}"]`);
            if (checkbox) {
                checkbox.checked = true;
                console.log('Отмечен чекбокс:', optionName);
            } else {
                console.warn('Чекбокс не найден:', optionName);
            }
        });
        
        // 6. Заполняем комментарий (если поле пустое)
        // Заполняем комментарий (ВСЕГДА при смене типа)
const commentTextarea = card.querySelector('.winery-comments');
if (commentTextarea && config.comment) {
    commentTextarea.value = config.comment;
}
        
        console.log('✅ Автозаполнение завершено для типа:', selectedType);
        
    } catch (error) {
        console.error('❌ Ошибка в автозаполнении:', error);
        // НЕ ПРЕРЫВАЕМ РАБОТУ ФОРМЫ!
        alert('Произошла небольшая ошибка в автозаполнении. Пожалуйста, заполните поля вручную.');
    }
}

// ИСПРАВЛЕННАЯ ФУНКЦИЯ ДОБАВЛЕНИЯ КАРТОЧКИ
function addWineryEquipmentCard() {
    console.log('🍷 Добавление карточки винодельческого оборудования...');
    
    try {
        // ВАЖНО: Ищем контейнер ВНУТРИ винодельческой формы
        let container = null;
        
        // Способ 1: Ищем в активной вкладке винодельческой формы
        const wineryForm = document.getElementById('winery-form');
        if (wineryForm && wineryForm.classList.contains('active')) {
            container = wineryForm.querySelector('.configurator-container');
            console.log('Найден контейнер в активной форме:', container);
        }
        
        // Способ 2: Если не нашли, ищем по относительному пути
        if (!container) {
            // Находим кнопку, которая была нажата
            const clickedBtn = document.querySelector('.winery-add-equipment-btn');
            if (clickedBtn) {
                // Ищем родительский контейнер configurator-container
                container = clickedBtn.closest('.configurator-container');
                console.log('Найден контейнер через кнопку:', container);
            }
        }
        
        // Способ 3: Прямой поиск в винодельческой форме
        if (!container) {
            container = document.querySelector('#winery-form .configurator-container');
            console.log('Прямой поиск контейнера:', container);
        }
        
        // ЕСЛИ КОНТЕЙНЕР НЕ НАЙДЕН - СОЗДАЕМ ЕГО
        if (!container) {
            console.error('❌ Контейнер не найден, создаем новый...');
            
            // Находим винодельческую форму
            const wineryFormContent = document.querySelector('#winery-form .tab-content');
            if (!wineryFormContent) {
                alert('Ошибка: не найдена форма винодельни');
                return;
            }
            
            // Ищем левую колонку
            const leftColumn = wineryFormContent.querySelector('.left-column');
            if (!leftColumn) {
                alert('Ошибка: не найдена левая колонка формы');
                return;
            }
            
            // Создаем контейнер если его нет
            container = leftColumn.querySelector('.configurator-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'configurator-container';
                leftColumn.appendChild(container);
                console.log('Создан новый контейнер');
            }
        }
        
        // Теперь добавляем карточку в найденный контейнер
        
        // 1. Подсчитываем сколько уже есть карточек
        const existingCards = container.querySelectorAll('.tank-configurator:not(.empty-card)');
        const newCardNumber = existingCards.length + 1;
        
        // 2. Создаем новую карточку
        const newCard = document.createElement('div');
        newCard.className = 'tank-configurator winery-tank';
        newCard.setAttribute('data-initialized', 'true');
        
        newCard.innerHTML = `
            <div class="config-header">
                <h4>Емкость ${newCardNumber}</h4>
                <button type="button" class="remove-equipment-btn" onclick="this.closest('.tank-configurator').remove()">× Удалить</button>
            </div>
            
            <!-- ТИП ОБОРУДОВАНИЯ -->
            <div class="form-group">
                <label>Тип винодельческого оборудования</label>
                <select class="winery-type-select" name="winery_equipment_type[]" onchange="handleWineryTypeChange(this)">
                    <option value="">-- Выберите тип оборудования --</option>
                    <option value="fermentation">Емкости для ферментации вина</option>
                    <option value="storage">Резервуары для выдержки и хранения</option>
                    <option value="sulfitation">Емкости для сульфитации</option>
                    <option value="clarification">Танки для холодного осветления</option>
                    <option value="filtration">Фильтрационное оборудование</option>
                    <option value="blending">Емкости для купажирования</option>
                    <option value="pasteurizer">Пастеризаторы для вина</option>
                    <option value="thermal">Терморегулируемые емкости</option>
                </select>
                <input type="hidden" class="winery-type-name" name="winery_equipment_type_name[]" value="">
            </div>
            
            <!-- ОБЪЕМ -->
            <div class="form-group">
                <label>Объем (литры)</label>
                <input type="number" min="100" max="50000" placeholder="от 100 до 50 000 литров" 
                       class="winery-volume-input" name="winery_volume[]" required>
            </div>
            
            <!-- МАТЕРИАЛ -->
            <div class="form-group">
                <label>Материал</label>
                <select class="winery-material-select" name="winery_material[]">
                    <option value="aisi304">AISI 304</option>
                    <option value="aisi316">AISI 316</option>
                    <option value="aisi316l">AISI 316L</option>
                </select>
            </div>
            
            <!-- ОПЦИИ -->
            <div class="equipment-grid">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="winery_heating[]" value="1" 
                           class="winery-option" data-option="heating">
                    <span style="color: #555;">Система нагрева</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="winery_cooling[]" value="1" 
                           class="winery-option" data-option="cooling">
                    <span style="color: #555;">Система охлаждения</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="winery_mixing[]" value="1" 
                           class="winery-option" data-option="mixing">
                    <span style="color: #555;">Автоматическая мешалка</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="winery_cip[]" value="1" 
                           class="winery-option" data-option="cip">
                    <span style="color: #555;">CIP-мойка</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="winery_insulation[]" value="1" 
                           class="winery-option" data-option="insulation">
                    <span style="color: #555;">Термоизоляция</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="winery_inertization[]" value="1" 
                           class="winery-option" data-option="inertization">
                    <span style="color: #555;">Система инертизации</span>
                </label>
            </div>
            
            <!-- КОММЕНТАРИЙ -->
            <div class="form-group">
                <label>Особые требования</label>
                <textarea class="winery-comments" 
                          placeholder="Укажите особые требования: системы инертизации, специфичные люки, датчики контроля и т.д."
                          name="winery_comment[]"></textarea>
            </div>
        `;
        
        // 3. Находим кнопку "Добавить еще оборудование" внутри контейнера
        const addButton = container.querySelector('.winery-add-equipment-btn');
        
        if (addButton) {
            // Вставляем перед кнопкой
            container.insertBefore(newCard, addButton);
        } else {
            // Или просто добавляем в конец контейнера
            container.appendChild(newCard);
        }
        
        console.log(`✅ Карточка #${newCardNumber} добавлена в правильный контейнер`);
        
        // 4. Назначаем обработчик для выбора типа оборудования
        const typeSelect = newCard.querySelector('.winery-type-select');
        if (typeSelect) {
            typeSelect.onchange = function() {
                handleWineryTypeChange(this);
            };
        }
        
        // 5. Прокручиваем к новой карточке
        setTimeout(() => {
            newCard.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'center' 
            });
            
            // Подсвечиваем на секунду
            newCard.style.boxShadow = '0 0 0 3px #F77C2A';
            newCard.style.transition = 'box-shadow 0.5s ease';
            
            setTimeout(() => {
                newCard.style.boxShadow = '';
            }, 1500);
        }, 100);
        
    } catch (error) {
        console.error('❌ Ошибка добавления карточки:', error);
        alert('Не удалось добавить оборудование. Пожалуйста, попробуйте снова или обновите страницу.');
    }
}

// ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ
document.addEventListener('DOMContentLoaded', function() {
    console.log('Инициализация винодельческой формы...');
    
    // Назначаем обработчик для кнопки добавления
    const wineryAddBtn = document.querySelector('.winery-add-equipment-btn');
    if (wineryAddBtn) {
        wineryAddBtn.onclick = addWineryEquipmentCard;
        console.log('✅ Кнопка добавления настроена');
    }
    
    // Назначаем обработчики для существующих селектов
    document.querySelectorAll('.winery-type-select').forEach(select => {
        select.onchange = function() {
            handleWineryTypeChange(this);
        };
    });
});

// ФУНКЦИЯ ДЛЯ МОЛОЧНОЙ ФОРМЫ
// Автозаполнение при выборе типа оборудования
function handleDairyTypeChange(selectElement) {
    console.log('Выбран тип молочного оборудования:', selectElement.value);
    
    try {
        const card = selectElement.closest('.tank-configurator');
        if (!card) return;
        
        // Данные для каждого типа
        const typeConfigs = {
            'reception': {
                placeholder: 'Рекомендуемый объем: 5000 л',
                material: 'aisi304',
                checkboxes: ['cooling', 'mixing', 'automation'],
                comment: 'Для приемки сырого молока, температурный контроль'
            },
            'cooling': {
                placeholder: 'Рекомендуемый объем: 3000 л',
                material: 'aisi316',
                checkboxes: ['cooling', 'insulation', 'automation'],
                comment: 'Хранение молока при +4°C, система охлаждения'
            },
            'fermentation': {
                placeholder: 'Рекомендуемый объем: 2000 л',
                material: 'aisi316',
                checkboxes: ['heating', 'cooling', 'mixing', 'cip', 'automation'],
                comment: 'Контроль температуры и pH для ферментации'
            },
            'processing': {
                placeholder: 'Рекомендуемый объем: 1500 л',
                material: 'aisi316',
                checkboxes: ['heating', 'mixing', 'cip'],
                comment: 'Закрытый тип, для сыроизготовления'
            },
            'curd': {
                placeholder: 'Рекомендуемый объем: 1000 л',
                material: 'aisi316',
                checkboxes: ['heating', 'cooling', 'mixing', 'cip'],
                comment: 'Производство творога, прессование'
            },
            'pasteurizer': {
                placeholder: 'Производительность (л/час)',
                material: 'aisi316',
                checkboxes: ['heating', 'cooling', 'automation'],
                comment: 'Пастеризация при 72-95°C, охлаждение'
            },
            'shelves': {
                placeholder: 'Количество полок/секций',
                material: 'aisi304',
                checkboxes: [],
                comment: 'Для созревания сыра, регулируемая влажность'
            },
            'salting': {
                placeholder: 'Рекомендуемый объем: 1000 л',
                material: 'aisi316',
                checkboxes: ['cooling'],
                comment: 'Соление сыра, контроль концентрации рассола'
            }
        };
        
        const selectedType = selectElement.value;
        const config = typeConfigs[selectedType];
        
        if (!config) return;
        
        // Сохраняем название типа
        const typeNameInput = card.querySelector('.dairy-type-name');
        if (typeNameInput) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            typeNameInput.value = selectedOption.text;
        }
        
        // Устанавливаем подсказку для объема
        const volumeInput = card.querySelector('.dairy-volume-input');
        if (volumeInput) {
            volumeInput.placeholder = config.placeholder;
            
            // Для стеллажей меняем тип поля
            if (selectedType === 'shelves') {
                volumeInput.type = 'text';
                volumeInput.value = '';
            } else {
                volumeInput.type = 'number';
                volumeInput.min = '100';
                volumeInput.max = '50000';
            }
        }
        
        // Устанавливаем материал
        const materialSelect = card.querySelector('.dairy-material-select');
        if (materialSelect) {
            materialSelect.value = config.material;
        }
        
        // Снимаем все отметки
        card.querySelectorAll('.dairy-option').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Отмечаем нужные чекбоксы
        config.checkboxes.forEach(optionName => {
            const checkbox = card.querySelector(`.dairy-option[data-option="${optionName}"]`);
            if (checkbox) checkbox.checked = true;
        });
        

        // Заполняем комментарий (ВСЕГДА при смене типа)
const commentTextarea = card.querySelector('.dairy-comments');
if (commentTextarea && config.comment) {
    commentTextarea.value = config.comment;
}
        
        console.log('✅ Автозаполнение завершено для типа:', selectedType);
        
    } catch (error) {
        console.error('❌ Ошибка в автозаполнении:', error);
    }
}

// Добавление новой карточки оборудования
function addDairyEquipmentCard() {
    console.log('🥛 Добавление карточки молочного оборудования...');
    
    try {
        // Ищем контейнер
        let container = null;
        
        // Способ 1: В активной форме
        const dairyForm = document.getElementById('dairy-form');
        if (dairyForm && dairyForm.classList.contains('active')) {
            container = dairyForm.querySelector('.configurator-container');
        }
        
        // Способ 2: Через кнопку
        if (!container) {
            const clickedBtn = document.querySelector('.dairy-add-equipment-btn');
            if (clickedBtn) {
                container = clickedBtn.closest('.configurator-container');
            }
        }
        
        // Способ 3: Прямой поиск
        if (!container) {
            container = document.querySelector('#dairy-form .configurator-container');
        }
        
        if (!container) {
            alert('Ошибка: не найден контейнер формы');
            return;
        }
        
        // Подсчитываем карточки
        const existingCards = container.querySelectorAll('.tank-configurator');
        const newCardNumber = existingCards.length + 1;
        
        // Создаем новую карточку
        const newCard = document.createElement('div');
        newCard.className = 'tank-configurator dairy-tank';
        newCard.setAttribute('data-initialized', 'true');
        
        newCard.innerHTML = `
            <div class="config-header">
                <h4>Оборудование ${newCardNumber}</h4>
                <button type="button" class="remove-equipment-btn" onclick="this.closest('.tank-configurator').remove()">× Удалить</button>
            </div>
            
            <!-- ТИП ОБОРУДОВАНИЯ -->
            <div class="form-group">
                <label>Тип молочного оборудования</label>
                <select class="dairy-type-select" 
                        name="dairy_equipment_type[]" 
                        onchange="handleDairyTypeChange(this)">
                    <option value="">-- Выберите тип оборудования --</option>
                    <option value="reception">Емкости для приемки молока</option>
                    <option value="cooling">Емкости для охлаждения/хранения молока</option>
                    <option value="fermentation">Ферментационные танки</option>
                    <option value="processing">Емкости для переработки (сыроизготовители)</option>
                    <option value="curd">Творогоизготовители</option>
                    <option value="pasteurizer">Пастеризаторы/охладители</option>
                    <option value="shelves">Стеллажи для созревания сыра</option>
                    <option value="salting">Контейнеры для соления сыра</option>
                </select>
                <input type="hidden" class="dairy-type-name" name="dairy_equipment_type_name[]" value="">
            </div>
            
            <!-- ОБЪЕМ -->
            <div class="form-group">
                <label>Объем/Количество</label>
                <input type="text" 
                       class="dairy-volume-input"
                       name="dairy_volume[]" 
                       placeholder="Укажите объем или количество" 
                       required>
                <small class="form-hint">Для емкостей: объем в литрах. Для стеллажей: количество полок/секций</small>
            </div>
            
            <!-- МАТЕРИАЛ -->
            <div class="form-group">
                <label>Материал</label>
                <select class="dairy-material-select" name="dairy_material[]">
                    <option value="aisi304">AISI 304</option>
                    <option value="aisi316">AISI 316</option>
                    <option value="aisi316l">AISI 316L</option>

                </select>
            </div>
            
            <!-- ОПЦИИ -->
            <div class="equipment-grid">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="dairy_heating[]" value="1" 
                           class="dairy-option" data-option="heating">
                    <span style="color: #555;">Система нагрева</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="dairy_cooling[]" value="1" 
                           class="dairy-option" data-option="cooling">
                    <span style="color: #555;">Система охлаждения</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="dairy_mixing[]" value="1" 
                           class="dairy-option" data-option="mixing">
                    <span style="color: #555;">Автоматическая мешалка</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="dairy_cip[]" value="1" 
                           class="dairy-option" data-option="cip">
                    <span style="color: #555;">CIP-мойка</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="dairy_insulation[]" value="1" 
                           class="dairy-option" data-option="insulation">
                    <span style="color: #555;">Термоизоляция</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="dairy_automation[]" value="1" 
                           class="dairy-option" data-option="automation">
                    <span style="color: #555;">Автоматизация</span>
                </label>
            </div>
            
            <!-- КОММЕНТАРИЙ -->
            <div class="form-group">
                <label>Особые требования</label>
                <textarea class="dairy-comments" 
                          placeholder="Укажите особые требования: датчики, люки, патрубки, система валидации и т.д."
                          name="dairy_comment[]"></textarea>
            </div>
        `;
        
        // Находим кнопку добавления
        const addButton = container.querySelector('.dairy-add-equipment-btn');
        
        if (addButton) {
            container.insertBefore(newCard, addButton);
        } else {
            container.appendChild(newCard);
        }
        
        console.log(`✅ Карточка #${newCardNumber} добавлена`);
        
        // Назначаем обработчик для выбора типа
        const typeSelect = newCard.querySelector('.dairy-type-select');
        if (typeSelect) {
            typeSelect.onchange = function() {
                handleDairyTypeChange(this);
            };
        }
        
        // Прокручиваем к новой карточке
        setTimeout(() => {
            newCard.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center'
            });
            
            newCard.style.boxShadow = '0 0 0 3px #3498db';
            setTimeout(() => {
                newCard.style.boxShadow = '';
            }, 1500);
        }, 100);
        
    } catch (error) {
        console.error('❌ Ошибка добавления карточки:', error);
        alert('Не удалось добавить оборудование. Пожалуйста, попробуйте снова.');
    }
}

// ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ
document.addEventListener('DOMContentLoaded', function() {
    console.log('🥛 Инициализация молочной формы...');
    
    // Назначаем обработчик для кнопки добавления
    const dairyAddBtn = document.querySelector('.dairy-add-equipment-btn');
    if (dairyAddBtn) {
        dairyAddBtn.onclick = addDairyEquipmentCard;
    }
    
    // Назначаем обработчики для существующих селектов
    document.querySelectorAll('.dairy-type-select').forEach(select => {
        select.onchange = function() {
            handleDairyTypeChange(this);
        };
    });
});
// Экспортируем функции в глобальную область
window.handleDairyTypeChange = handleDairyTypeChange;
window.addDairyEquipmentCard = addDairyEquipmentCard;

// ФУНКЦИЯ: ПЕРЕХОД К ФОРМЕ С ВЫБРАННЫМ ТИПОМ ЕМКОСТИ
function scrollToTankType(tankType) {
    console.log('🔄 Переход к расчету для типа:', tankType);
    
    // 1. Прокручиваем к форме расчета
    const calculationSection = document.getElementById('calculation');
    if (calculationSection) {
        calculationSection.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }
    
    // 2. Через 0.5 секунды переключаем на вкладку "Подробный расчет"
    setTimeout(function() {
        // Находим кнопку вкладки "Подробный расчет"
        const detailedTabBtn = document.querySelector('.tab-btn[data-tab="detailed"]');
        const detailedForm = document.getElementById('detailed-form');
        
        if (detailedTabBtn && detailedForm) {
            // Убираем активность у всех вкладок
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Убираем активность у всех форм
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Активируем вкладку "Подробный расчет"
            detailedTabBtn.classList.add('active');
            detailedForm.classList.add('active');
            
            // 3. Выбираем тип емкости в форме
            setTimeout(function() {
                selectTankTypeInForm(tankType);
            }, 300);
        }
    }, 500);
}

// ФУНКЦИЯ: ВЫБРАТЬ ТИП ЕМКОСТИ В ФОРМЕ
function selectTankTypeInForm(tankType) {
    console.log('🎯 Устанавливаем тип емкости в форме:', tankType);
    
    // Находим выпадающий список в первой емкости
    const tankTypeSelect = document.getElementById('tank-type-1');
    
    if (!tankTypeSelect) {
        console.error('❌ Не найден select для выбора типа емкости');
        return;
    }
    
    // Соответствие названий
    const typeMapping = {
        'storage': 'storage',
        'mixing': 'mixing',
        'thermal': 'thermal',
        'pressure': 'pressure',
        'sip': 'sip',
        'heat-exchanger': 'heat-exchanger'
    };
    
    // Выбираем нужный тип
    const valueToSelect = typeMapping[tankType];
    
    if (valueToSelect) {
        tankTypeSelect.value = valueToSelect;
        console.log('✅ Выбран тип:', tankTypeSelect.value);
        
        // Автоматически отмечаем типичные опции для этого типа
        autoSelectOptionsForType(valueToSelect);
    }
}

// ФУНКЦИЯ: АВТОМАТИЧЕСКИ ОТМЕЧАЕМ ОПЦИИ ПО ТИПУ
function autoSelectOptionsForType(tankType) {
    console.log('⚙️ Автовыбор опций для типа:', tankType);
    
    // Находим первую карточку емкости
    const firstTank = document.getElementById('tank-1');
    if (!firstTank) return;
    
    // Сначала снимаем все отметки
    firstTank.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Правила для каждого типа
    const typeRules = {
        'storage': [], // для хранения обычно без опций
        'mixing': ['mixer_1'], // только мешалка
        'thermal': ['heating_1', 'cooling_1'], // нагрев + охлаждение
        'pressure': [], // для давления особых опций нет
        'sip': ['sip_1'], // только SIP
        'heat-exchanger': ['heating_1', 'cooling_1'] // и нагрев и охлаждение
    };
    
    // Отмечаем нужные чекбоксы
    const checkboxesToCheck = typeRules[tankType] || [];
    
    checkboxesToCheck.forEach(checkboxName => {
        const checkbox = firstTank.querySelector(`[name="${checkboxName}"]`);
        if (checkbox) {
            checkbox.checked = true;
            console.log('✅ Отмечен чекбокс:', checkboxName);
        }
    });
    
    // Показываем подсказку пользователю
    showTankTypeHint(tankType);
}

// ФУНКЦИЯ: ПОКАЗАТЬ ПОДСКАЗКУ
function showTankTypeHint(tankType) {
    // Удаляем старую подсказку если есть
    const oldHint = document.querySelector('.tank-type-hint');
    if (oldHint) oldHint.remove();
    
    // Тексты подсказок
    const hints = {
        'storage': 'Для емкости хранения вы можете добавить насос для перекачки',
        'mixing': 'Для емкости с мешалкой выберите скорость вращения в комментариях',
        'thermal': 'Укажите нужный температурный диапазон в комментариях',
        'pressure': 'Укажите рабочее давление (до 6 бар)',
        'sip': 'Укажите требования к стерилизации',
        'heat-exchanger': 'Укажите производительность теплообменника'
    };
    
    const hintText = hints[tankType];
    if (!hintText) return;
    
    // Создаем элемент подсказки
    const hint = document.createElement('div');
    hint.className = 'tank-type-hint';
    hint.innerHTML = `
        <div style="
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #2c3e50;
        ">
            <strong>💡 Подсказка:</strong> ${hintText}
        </div>
    `;
    
    // Вставляем после выпадающего списка
    const tankTypeSelect = document.getElementById('tank-type-1');
    if (tankTypeSelect && tankTypeSelect.parentNode) {
        tankTypeSelect.parentNode.insertBefore(hint, tankTypeSelect.nextSibling);
    }
    
    // Убираем через 10 секунд
    setTimeout(() => {
        if (hint.parentNode) {
            hint.style.opacity = '0';
            hint.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                if (hint.parentNode) hint.parentNode.removeChild(hint);
            }, 500);
        }
    }, 10000);
}

// ДОБАВЛЯЕМ СТИЛИ ДЛЯ ПОДСКАЗКИ (в style.css)
function addTankTypeStyles() {
    // Проверяем, есть ли уже стили
    if (document.getElementById('tank-type-styles')) return;
    
    const style = document.createElement('style');
    style.id = 'tank-type-styles';
    style.textContent = `
        /* Анимация для подсветки формы */
        .tank-type-selected {
            animation: pulse-highlight 2s ease;
        }
        
        @keyframes pulse-highlight {
            0% { box-shadow: 0 0 0 0 rgba(247, 124, 42, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(247, 124, 42, 0); }
            100% { box-shadow: 0 0 0 0 rgba(247, 124, 42, 0); }
        }
        
        /* Стили для выпадающего списка типа емкости */
        .tank-type-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            color: #333;
            background-color: white;
            transition: all 0.3s;
        }
        
        .tank-type-select:focus {
            border-color: #F77C2A;
            box-shadow: 0 0 0 3px rgba(247, 124, 42, 0.2);
            outline: none;
        }
        
        /* Подсветка активного типа */
        .tank-type-select option[selected] {
            background-color: #F77C2A;
            color: white;
        }
    `;
    
    document.head.appendChild(style);
}

// ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ СТРАНИЦЫ
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Инициализация системы емкостей');
    
    // Инициализируем существующие емкости
    initializeExistingTanks();
    
    // Делаем функции доступными глобально
    window.addTankCard = addTankCard;
    window.scrollToTankType = scrollToTankType;
    
    console.log('✅ Система емкостей готова');
});

// Экспортируем функции для глобального использования
if (typeof window !== 'undefined') {
    window.addTankCard = addTankCard;
    window.scrollToTankType = scrollToTankType;
    window.autoSelectOptionsForTank = autoSelectOptionsForTank;
}
function scrollToBreweryTab(lotNumber = 'lot3') {
    console.log('Переход на расчет пивоварни с лотом:', lotNumber);
    
    const calculationSection = document.getElementById('calculation');
    if (!calculationSection) return;
    
    // Прокручиваем к форме расчета
    calculationSection.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
    
    // Задержка для плавности
    setTimeout(() => {
        // НАХОДИМ И АКТИВИРУЕМ ВКЛАДКУ ПИВОВАРНИ
        const breweryTabBtn = document.querySelector('.tab-btn[data-tab="brewery"]');
        
        if (breweryTabBtn) {
            console.log('Найдена вкладка пивоварни, активируем...');
            
            // Снимаем активность со всех вкладок
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Активируем вкладку пивоварни
            breweryTabBtn.classList.add('active');
            
            // Показываем форму пивоварни
            const breweryForm = document.getElementById('brewery-form');
            if (breweryForm) {
                breweryForm.style.display = 'block';
                breweryForm.classList.add('active');
            }
            
            // Выбираем лот 3 (1000 л)
            setTimeout(() => {
                const lotSelect = document.getElementById('brewery-lot-select');
                if (lotSelect) {
                    lotSelect.value = lotNumber;
                    
                    // Запускаем событие change
                    const changeEvent = new Event('change', {
                        bubbles: true,
                        cancelable: true
                    });
                    lotSelect.dispatchEvent(changeEvent);
                    
                    // Ручной вызов загрузки данных
                    if (typeof loadBreweryLotData === 'function') {
                        setTimeout(() => {
                            loadBreweryLotData(lotNumber);
                        }, 200);
                    }
                }
            }, 300);
            
            // Подсветка
            if (breweryForm) {
                breweryForm.style.boxShadow = '0 0 0 3px #F77C2A';
                breweryForm.style.transition = 'box-shadow 0.5s ease';
                
                setTimeout(() => {
                    breweryForm.style.boxShadow = 'none';
                }, 500);
            }
            
        } else {
            console.error('Вкладка пивоварни не найдена!');
            // Если не нашли вкладку пивоварни, используем общую функцию
            scrollToCalculationForm(lotNumber);
        }
    }, 400);
}

// Экспортируем
window.scrollToBreweryTab = scrollToBreweryTab;
function scrollToDairyWithShelves() {
    console.log('Переход на молочный расчет с выбором стеллажей для сыра');
    
    const calculationSection = document.getElementById('calculation');
    if (!calculationSection) return;
    
    // Прокручиваем к форме расчета
    calculationSection.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
    });
    
    setTimeout(() => {
        // Активируем вкладку молочного оборудования
        activateTab('dairy');
        
        setTimeout(() => {
            // Автоматически выбираем тип оборудования "Стеллажи для созревания сыра"
            const dairyForm = document.getElementById('dairy-form');
            if (dairyForm) {
                // Находим первый select типа оборудования
                const firstTypeSelect = dairyForm.querySelector('.dairy-type-select');
                if (firstTypeSelect) {
                    firstTypeSelect.value = 'shelves';
                    
                    // Запускаем обработчик изменения
                    const changeEvent = new Event('change');
                    firstTypeSelect.dispatchEvent(changeEvent);
                    
                    // Если нужно, заполняем дополнительные поля
                    setTimeout(() => {
                        // Заполняем количество (например, 10 полок)
                        const volumeInput = dairyForm.querySelector('.dairy-volume-input');
                        if (volumeInput) {
                            volumeInput.value = '10';
                            volumeInput.placeholder = 'Количество полок/секций';
                        }
                        
                        // Можем добавить комментарий
                        const commentTextarea = dairyForm.querySelector('.dairy-comments');
                        if (commentTextarea && !commentTextarea.value.trim()) {
                            commentTextarea.value = 'Нужны стеллажи для созревания сыра, как в примере проекта. Требуется регулируемая влажность и вентиляция.';
                        }
                    }, 200);
                }
            }
            
            // Подсветка формы
            if (dairyForm) {
                dairyForm.style.boxShadow = '0 0 0 3px #F77C2A';
                dairyForm.style.transition = 'box-shadow 0.5s ease';
                
                setTimeout(() => {
                    dairyForm.style.boxShadow = 'none';
                }, 500);
            }
        }, 300);
    }, 400);
}

// Экспортируем
window.scrollToDairyWithShelves = scrollToDairyWithShelves;

document.addEventListener('DOMContentLoaded', function() {
    // Находим все ссылки с телефоном
    const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
    
    phoneLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Отправляем событие в Яндекс.Метрику
            if (typeof ym !== 'undefined') {
                ym(ВАШ_НОМЕР_СЧЁТЧИКА, 'reachGoal', 'phone_click');
                // Если используете несколько целей:
                // ym(ВАШ_НОМЕР_СЧЁТЧИКА, 'reachGoal', 'phone');
            }
        });
    });
});

// ЦЕЛИ ДЛЯ ФОРМ ЗАЯВОК (Яндекс.Метрика ID: 109477134)
// Основная функция отправки целей
function trackYandexMetricaGoal(goalName) {
    if (typeof ym !== 'undefined') {
        ym(109477134, 'reachGoal', goalName);
    }
}

// 1. Форма подробного расчета
const detailedForm = document.querySelector('#detailed-form form');
if (detailedForm) {
    detailedForm.addEventListener('submit', function() {
        setTimeout(() => trackYandexMetricaGoal('detailed_form'), 300);
    });
}

// 2. Форма пивоварни
const breweryForm = document.querySelector('#brewery-form form');
if (breweryForm) {
    breweryForm.addEventListener('submit', function() {
        setTimeout(() => trackYandexMetricaGoal('brewery_form'), 300);
    });
}

// 3. Форма молочного оборудования
const dairyForm = document.querySelector('#dairy-form form');
if (dairyForm) {
    dairyForm.addEventListener('submit', function() {
        setTimeout(() => trackYandexMetricaGoal('dairy_form'), 300);
    });
}

// 4. Форма винодельческого оборудования
const wineryForm = document.querySelector('#winery-form form');
if (wineryForm) {
    wineryForm.addEventListener('submit', function() {
        setTimeout(() => trackYandexMetricaGoal('winery_form'), 300);
    });
}

// 5. Быстрая заявка
const quickForm = document.querySelector('#quick-form form');
if (quickForm) {
    quickForm.addEventListener('submit', function() {
        setTimeout(() => trackYandexMetricaGoal('quick_form'), 300);
    });
}
// Привяжите обработчик через JavaScript
document.addEventListener('DOMContentLoaded', function() {
    var articlesBtn = document.getElementById('footer-articles-btn');
    if (articlesBtn) {
        articlesBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Ваша функция
            const modal = document.getElementById('articles-modal');
            document.querySelectorAll('[id*="modal"]').forEach(m => m.style.display = 'none');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            activateArticleTab('beer');
            
            return false;
        };
    }
});

// Прокрутка при клике на кнопку комплектации
function scrollToBreweryLotOnClick(configId) {
    console.log('🔄 Прокрутка к лоту:', configId);
    
    // 1. Находим блок информации о лоте
    const lotInfoSection = document.getElementById(configId + '-info');
    if (!lotInfoSection) {
        console.error('Не найден блок для лота:', configId);
        return;
    }
    
    // 2. Показываем блок (на всякий случай)
    lotInfoSection.style.display = 'block';
    lotInfoSection.classList.add('active');
    
    // 3. Вычисляем позицию для прокрутки с отступом
    const headerHeight = document.querySelector('.header').offsetHeight || 100;
    const offset = 30; // Дополнительный отступ
    
    // Позиция блока лота минус высота шапки и отступ
    const lotPosition = lotInfoSection.getBoundingClientRect().top + window.pageYOffset;
    const scrollToPosition = lotPosition - headerHeight - offset;
    
    // 4. Плавная прокрутка
    window.scrollTo({
        top: scrollToPosition,
        behavior: 'smooth'
    });
    
    // 5. Подсвечиваем блок на 2 секунды
    lotInfoSection.style.boxShadow = '0 0 0 3px #F77C2A';
    lotInfoSection.style.transition = 'box-shadow 0.3s ease';
    
    setTimeout(() => {
        lotInfoSection.style.boxShadow = '';
    }, 2000);
    
    console.log('✅ Прокручено к лоту:', configId);
}

// Инициализация обработчиков для кнопок комплектаций
function initBreweryConfigButtons() {
    console.log('🔧 Инициализация кнопок комплектаций пивоварен...');
    
    // Находим все кнопки комплектаций
    const configButtons = document.querySelectorAll('.config-btn[data-config]');
    
    configButtons.forEach(button => {
        // Убираем старые обработчики
        button.onclick = null;
        
        // Добавляем новый обработчик
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const configId = this.getAttribute('data-config');
            console.log('Клик на кнопку комплектации:', configId);
            
            // 1. Активируем кнопку
            configButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // 2. Показываем соответствующий блок информации
            const allInfoSections = document.querySelectorAll('.config-info-section');
            allInfoSections.forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            
            const targetSection = document.getElementById(configId + '-info');
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('active');
            }
            
            // 3. Прокручиваем к блоку
            setTimeout(() => {
                scrollToBreweryLotOnClick(configId);
            }, 100);
            
            return false;
        });
    });
    
    console.log('✅ Кнопки инициализированы:', configButtons.length);
}

// Запускаем при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Ждем немного, чтобы все элементы загрузились
    
    setTimeout(initBreweryConfigButtons, 500);
    
    // Также повторяем инициализацию при переходе на категорию пивоварен
    const breweryBtn = document.querySelector('.category-btn[data-category="brewery"]');
    if (breweryBtn) {
        breweryBtn.addEventListener('click', function() {
            setTimeout(initBreweryConfigButtons, 800);
        });
    }
});

// Экспортируем функцию для глобального использования
window.scrollToBreweryLotOnClick = scrollToBreweryLotOnClick;