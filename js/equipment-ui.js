
// Функция создания короткой карточки (ИСПРАВЛЕННАЯ ВЕРСИЯ)
function createShortCard(item) {
    const specEntries = Object.entries(item.shortSpec).slice(0, 4);
    
    return `
        <div class="image-container" 
             style="min-height: 200px; height: auto; max-height: 350px; overflow: hidden; border-radius: 12px 12px 0 0; 
             display: flex; align-items: center; justify-content: center; position: relative; cursor: pointer;">
            <img src="${item.image || item.altImage}" 
                 alt="${item.name}" 
                 id="image-${item.id}"
                 data-default="${item.image}"
                 data-alternate="${item.image || item.altImage}"
                 style="width: auto; height: auto; max-width: 90%; max-height: 300px; object-fit: contain; display: block; transition: all 0.3s ease; margin: 10px auto;"
                 onerror="this.src='beer-mash-tun.jpg'"
                 onclick="handleImageClick('${item.id}')">
            <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; 
                 padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; display: none;" id="image-hint-${item.id}">
                Нажмите для подробностей
            </div>
        </div>
        <div style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
            <h3 style="margin: 0 0 20px; color: #2d3748; font-size: 1.4rem; line-height: 1.3;">
                ${item.name}
            </h3>
            <div style="background: #ffffff; border-radius: 10px; overflow: hidden; border: 1px solid #e0e0e0; margin-bottom: 25px;">
    ${specEntries.map(([key, value], index) => `
        <div style="display: flex; padding: ${index === specEntries.length - 1 ? '14px' : '14px 14px'}; 
             background-color: ${index % 2 === 0 ? '#ffffff' : '#f5f5f5'}; 
             ${index !== specEntries.length - 1 ? 'border-bottom: 1px solid #eeeeee;' : ''}">
            <div style="flex: 0 0 100px; font-weight: 600; color: #555555; font-size: 0.9rem; 
                 padding-right: 10px; display: flex; align-items: center;">
                ${key}
            </div>
            <div style="flex: 1; color: #333333; font-size: 0.9rem; line-height: 1.5; 
                 display: flex; align-items: center;">
                ${value}
            </div>
        </div>
    `).join('')}
</div>
            <button onclick="showEquipmentModal('${item.id}')" 
                    class="details-btn"
                    style="width: 100%; padding: 12px; background: linear-gradient(135deg, #2c5282, #2b2b39); 
                           color: white; border: none; border-radius: 10px; font-weight: 600; 
                           font-size: 1rem; cursor: pointer; transition: all 0.3s; margin-top: auto;
                           box-shadow: 0 4px 12px rgba(44, 82, 130, 0.25);">
                Подробные характеристики
            </button>
        </div>
    `;
}

    // Функция обработки клика по картинке
    // Функция обработки клика по картинке
function handleImageClick(itemId) {
    const imgElement = document.getElementById(`image-${itemId}`);
    const hintElement = document.getElementById(`image-hint-${itemId}`);
    
    if (!imgElement) return;
    
    // Сразу открываем модальное окно
    showEquipmentModal(itemId);
    
    // Показываем подсказку
    if (hintElement) {
        hintElement.style.display = 'block';
        setTimeout(() => {
            hintElement.style.display = 'none';
        }, 2000);
    }
    
    // Анимация
    imgElement.style.transform = 'scale(1.05)';
    setTimeout(() => {
        imgElement.style.transform = 'scale(1)';
    }, 300);
}

    // Функция создания модального окна
    // Функция создания модального окна с галереей
function createEquipmentModal(item) {
    const currentVolume = window.selectedVolumes[item.id] || item.defaultVolume;
    
    // Массив изображений для оборудования (основное + дополнительные)
    const equipmentImages = {
        'beer-1': ['beer-mash-tun.jpg', 'beer0.png'],
        'beer-2': ['beer-mash-kettle.jpg', 'beer1.png'],
        'beer-3': ['beer-filter-tun.jpg', 'beer2.png'],
        'beer-4': ['beer-wort-kettle.jpg', 'beer3.png'],
        'beer-5': ['beer-whirlpool.jpg', 'beer4.png'],
        'beer-6': ['beer-hot-water-tank.jpg', 'beer5.png'],
        'beer-7': ['beer-ckt.jpg', 'beer6.png'],
        'beer-8': ['beer-wort-collector.jpg', 'wort-collector.png'],
        'beer-9': ['beer-farfas.jpg', 'fermentation-vat.png'],
        'aux-1': ['aux-crusher.jpg', 'crusher.png'],
        'aux-2': ['aux-boiler.jpg', 'boiler.png'],
        'aux-3': ['aux-chiller.jpg', 'chiller.png'],
        'aux-4': ['aux-heat-exchanger.jpg', 'pasteurizer.png']
    };
    
    // Получаем массив изображений для текущего оборудования
    const images = equipmentImages[item.id] || [item.image, item.altImage || item.image];
    
    return `
        <div id="equipment-modal-${item.id}" class="equipment-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box; overflow-y: auto;">
            <div class="modal-content" style="background: white; border-radius: 16px; max-width: 1100px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative;">
                <div style="background: linear-gradient(135deg, #f7f7f7 0%, #89899f 20%, var(--dark-bg) 50%, #222230 80%, #1a1a26 100%); padding: 10px 15px; border-radius: 16px 16px 0 0; position: sticky; top: 0; z-index: 10;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                        <div style="flex-shrink: 0; display: flex; align-items: center;">
                            <img src="logoblack.png" alt="Логотип" style="width: 90px; height: 90px; object-fit: contain; border-radius: 8px;">
                        </div>
                        <h3 style="margin: 0; color: white; font-size: 1.6rem; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: center; padding: 0 10px;">
                            ${item.name}
                        </h3>
                        <button onclick="closeEquipmentModal('${item.id}')" 
                                style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 8px 12px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: 500; transition: all 0.3s; font-size: 0.9rem; white-space: nowrap; flex-shrink: 0;"
                                onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                            назад
                        </button>
                    </div>
                </div>
                <div style="padding: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="">
                        <!-- Основное фото -->
                        <div style="width: 280px; height: 280px; background: #f8fafc; border-radius: 15px; 
                             display: flex; align-items: center; justify-content: center; margin-bottom: 15px; 
                             border: 1px solid #e2e8f0; overflow: hidden; cursor: pointer;">
                            <img src="${images[0]}" alt="${item.name}" 
                                 id="main-modal-image-${item.id}"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='beer-mash-tun.jpg'">
                        </div>
                        
                        <!-- Миниатюры -->
                        <div style="display: flex; gap: 10px; justify-content: left; flex-wrap: wrap; margin-bottom: 25px;">
                            ${images.map((img, index) => `
                                <div style="width: 60px; height: 60px; border-radius: 8px; border: ${index === 0 ? '2px solid #f77c2a' : '1px solid #e2e8f0'}; 
                                     overflow: hidden; cursor: pointer; background: #f8fafc; display: flex; align-items: center; justify-content: center;"
                                     onclick="changeMainImage('${item.id}', ${index}, '${img}')">
                                    <img src="${img}" alt="Фото ${index + 1}" 
                                         style="width: 100%; height: 100%; object-fit: cover;"
                                         onerror="this.src='beer-mash-tun.jpg'">
                                </div>
                            `).join('')}
                        </div>
                        
                        <div style="margin-bottom: 30px;">
                            <div style="font-weight: 600; color: #2d3748; margin-bottom: 15px; font-size: 1.1rem; 
                                 display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 20px;">📦</span> Выберите объем:
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;" id="volume-buttons-${item.id}">
                                ${item.availableVolumes.map(volume => {
                                    const isSelected = currentVolume === volume;
                                    return `
                                        <button type="button" 
                                                class="volume-btn"
                                                data-volume="${volume}"
                                                data-item="${item.id}"
                                                onclick="selectVolume('${item.id}', '${volume}')"
                                                style="padding: 14px; text-align: center; border: 2px solid ${isSelected ? '#f77c2a' : '#e2e8f0'}; 
                                                       border-radius: 10px; background: ${isSelected ? '#fff5eb' : 'white'};
                                                       color: ${isSelected ? '#f77c2a' : '#4a5568'}; 
                                                       font-weight: ${isSelected ? '600' : '400'};
                                                       cursor: pointer; transition: all 0.2s; outline: none;">
                                            ${volume}
                                        </button>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                        
                        <div style="margin: 25px 0; padding: 20px; background: linear-gradient(135deg, #fff0e6, #ffebe0); 
                             border-radius: 12px; border-left: 4px solid #f77c2a; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1; color: #f77c2a;">⚡</div>
                            <div style="display: flex; align-items: flex-start; gap: 15px;">
                                <div style="background: #f77c2a; color: white; width: 32px; height: 32px; 
                                     border-radius: 8px; display: flex; align-items: center; justify-content: center; 
                                     font-weight: bold; flex-shrink: 0;">
                                    !
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #d35400; margin-bottom: 8px; font-size: 1.05rem;">
                                        ${getSpecialAdvantage(item.name)}
                                    </div>
                                    <div style="color: #e67e22; line-height: 1.5; font-size: 0.95rem;">
                                        ${getAdvantageDescription(item.name)}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 25px; padding: 20px; background: linear-gradient(135deg, #f8fafc, #f0f7ff); 
                             border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #2c5282, #4a90e2); 
                                     border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                    ₽
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #2c5282; font-size: 1.2rem; line-height: 1.2;">
                                        Цена оборудования
                                    </div>
                                    <div style="font-size: 0.85rem; color: #718096; margin-top: 2px;">
                                        за выбранный объем
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: center; padding: 18px 0; border-radius: 8px; 
                                 background: white; border: 2px dashed #e2e8f0; margin-bottom: 15px;">
                                <div style="font-size: 1.1rem; font-weight: 600; color: #4a5568; display: flex; 
                                     align-items: center; justify-content: center; gap: 8px;">
                                    <span style="font-size: 20px;"></span>
                                    По запросу
                                </div>
                            </div>
                            <div style="font-size: 0.8rem; color: #718096; line-height: 1.4; text-align: center;">
                                Точная стоимость зависит от комплектации, объема и дополнительных опций
                            </div>
                        </div>
                        
                        <button onclick="calculateEquipment('${item.id}', '${item.name}')" 
                                style="width: 100%; padding: 18px; background: linear-gradient(135deg, #F77C2A, #E55A00); 
                                       color: white; border: none; border-radius: 12px; font-weight: 600; 
                                       font-size: 1.1rem; cursor: pointer; transition: all 0.3s; 
                                       display: flex; align-items: center; justify-content: center; gap: 10px;
                                       box-shadow: 0 4px 15px rgba(0, 168, 107, 0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 168, 107, 0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 168, 107, 0.3)'">
                            <span style="font-size: 20px;"></span> Рассчитать стоимость
                        </button>
                    </div>
                    
                    <div style="flex: 1; min-width: 300px;">
                        <div style="background: #f8fafc; padding: 30px; border-radius: 15px; margin-bottom: 30px; 
                             border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <h4 style="margin-top: 0; color: #2c5282; margin-bottom: 25px; font-size: 1.4rem; 
                                padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                                🛠️ Полные технические характеристики
                            </h4>
                            <div style="color: #4a5568; line-height: 1.6;">
                                ${Object.entries(item.detailedDescription).map(([key, value], index) => `
                                    <div style="margin-bottom: ${index === Object.entries(item.detailedDescription).length - 1 ? '0' : '22px'}; 
                                         padding-bottom: ${index === Object.entries(item.detailedDescription).length - 1 ? '0' : '22px'}; 
                                         ${index !== Object.entries(item.detailedDescription).length - 1 ? 'border-bottom: 1px dashed #e2e8f0;' : ''}">
                                        <div style="font-weight: 700; color: #2c5282; margin-bottom: 10px; font-size: 1.05rem; 
                                             display: flex; align-items: center; gap: 8px;">
                                            <span style="background: #ebf8ff; width: 24px; height: 24px; border-radius: 6px; 
                                                  display: flex; align-items: center; justify-content: center; font-size: 12px; color: #2c5282;">
                                                ${index + 1}
                                            </span>
                                            ${key}
                                        </div>
                                        <div style="color: #4a5568; padding-left: 32px;">${value}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #fff8eb, #fff5e6); padding: 25px; border-radius: 12px; 
                             border-left: 5px solid #f77c2a; box-shadow: 0 4px 12px rgba(247, 124, 42, 0.1);">
                            <div style="display: flex; align-items: flex-start; gap: 18px;">
                                <span style="font-size: 28px; color: #f77c2a; flex-shrink: 0;">💡</span>
                                <div>
                                    <div style="font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 1.1rem;">
                                        Нужна консультация или индивидуальный проект?
                                    </div>
                                    <div style="color: #718096; line-height: 1.5;">
                                        Наши инженеры помогут подобрать оптимальную комплектацию под ваши задачи. 
                                        Выберите объем, нажмите "Рассчитать стоимость" для перехода к форме расчета 
                                        с автоматическим заполнением данных.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 30px; width: 100%;">
                            <a href="#" 
                               onclick="closeEquipmentModal('${item.id}'); 
                                        document.querySelector('.category-tabs').scrollIntoView({behavior: 'smooth'}); 
                                        return false;"
                               style="color: #f77c2a; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border: 1px solid #f77c2a; border-radius: 6px; transition: all 0.3s;">
                                ← К каталогу оборудования
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Функция смены главного изображения при клике на миниатюру
function changeMainImage(itemId, index, imgSrc) {
    const mainImage = document.getElementById(`main-modal-image-${itemId}`);
    if (!mainImage) return;
    
    // Меняем основное изображение
    mainImage.src = imgSrc;
    
    // Обновляем рамки у миниатюр
    const thumbnails = document.querySelectorAll(`#equipment-modal-${itemId} .thumbnail-container`);
    thumbnails.forEach((thumb, thumbIndex) => {
        if (thumbIndex === index) {
            thumb.style.border = '2px solid #f77c2a';
        } else {
            thumb.style.border = '1px solid #e2e8f0';
        }
    });
    
    // Анимация
    mainImage.style.transform = 'scale(1.05)';
    setTimeout(() => {
        mainImage.style.transform = 'scale(1)';
    }, 300);
}


    // Основные функции управления
    function showEquipmentModal(itemId) {
        window._modalScrollPos = window.scrollY;
        const newUrl = `${window.location.pathname}?equipment=${itemId}`;
        window.history.pushState({}, '', newUrl);
        const item = beerEquipmentData.equipment.find(eq => eq.id === itemId);
        if (!item) return;
        
        // Сбрасываем состояние картинки
        window.imageStates[itemId] = 1;
        
        let modal = document.getElementById(`equipment-modal-${itemId}`);
        if (!modal) {
            const modalHTML = createEquipmentModal(item);
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            modal = document.getElementById(`equipment-modal-${itemId}`);
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        const volume = window.selectedVolumes[itemId];
        if (volume) {
            setTimeout(() => {
                selectVolume(itemId, volume);
            }, 100);
        }
    }

    function closeEquipmentModal(itemId) {
    const modal = document.getElementById(`equipment-modal-${itemId}`);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        window.history.replaceState({}, '', window.location.pathname);
    }
    
    // Сбрасываем состояние картинки
    window.imageStates[itemId] = 1;
    
    // Возвращаем картинку в ИСХОДНОЕ состояние (как при загрузке страницы)
    const imgElement = document.getElementById(`image-${itemId}`);
    if (imgElement) {
        const alternateImg = imgElement.getAttribute('data-alternate');
        imgElement.src = alternateImg;
        
        // Возвращаем исходные стили (НЕ cover, а contain с отступами)
        imgElement.style.width = 'auto';
        imgElement.style.height = 'auto';
        imgElement.style.maxWidth = '90%';
        imgElement.style.maxHeight = '300px';
        imgElement.style.objectFit = 'contain';
        imgElement.style.margin = '10px auto';
        imgElement.style.transform = 'scale(1)';
    }
    
    if (window._modalScrollPos !== undefined) {
        window.scrollTo(0, window._modalScrollPos);
        window._modalScrollPos = undefined;
    }
}

    // Функция для открытия формы с данными
    function openDetailedFormWithData(itemName, selectedVolume, itemId) {
        console.log('📋 Открываем форму с данными для:', itemName, selectedVolume);
        
        // 1. Переключаемся на вкладку "Подробный расчет"
        const detailedTabBtn = document.querySelector('[data-tab="detailed"]');
        const detailedForm = document.getElementById('detailed-form');
        
        if (!detailedTabBtn || !detailedForm) {
            console.error('Форма подробного расчета не найдена');
            alert('Форма расчета не найдена на странице');
            return;
        }
        
        // Переключаемся на вкладку
        const allTabs = document.querySelectorAll('.tab-btn');
        const allForms = document.querySelectorAll('.tab-content');
        
        allTabs.forEach(tab => tab.classList.remove('active'));
        allForms.forEach(form => {
            form.classList.remove('active');
            form.style.display = 'none';
        });
        
        detailedTabBtn.classList.add('active');
        detailedForm.classList.add('active');
        detailedForm.style.display = 'block';
        
        // 2. Заменяем типы в форме на пивные
        setTimeout(() => {
            replaceTankTypesToBrewery();
            
            // 3. Находим элемент оборудования для получения всех данных
            const equipmentItem = beerEquipmentData.equipment.find(eq => eq.id === itemId);
            
            if (equipmentItem) {
                // 4. Заполняем форму данными
                fillFormWithEquipmentData(equipmentItem, selectedVolume);
                
                // 5. Показываем уведомление
                showSuccessNotification(itemName, selectedVolume);
            } else {
                console.error('Оборудование не найдено:', itemId);
            }
            
            // 6. Прокручиваем к форме
            setTimeout(() => {
                detailedForm.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start'
                });
            }, 500);
        }, 200);
    }

    // ФУНКЦИЯ ДЛЯ ЗАПОЛНЕНИЯ ФОРМЫ ДАННЫМИ
    function fillFormWithEquipmentData(item, selectedVolume) {
        console.log('🔄 Заполняем форму данными для:', item.name);
        
        // 1. Находим первую емкость в форме
        const firstTank = document.getElementById('tank-1');
        if (!firstTank) {
            console.error('❌ Емкость tank-1 не найдена!');
            return;
        }
        
        // 2. Маппинг названий на значения селектора
        const nameToValueMap = {
            'ЗАТОРНЫЙ АППАРАТ': 'brewing-kettle',
            'ЗАТОРНО-СУСЛОВАРОЧНЫЙ АППАРАТ': 'brewhouse',
            'ФИЛЬТРАЦИОННЫЙ АППАРАТ': 'lauter-tun',
            'ФИЛЬТРАЦИОННЫЙ АППАРАТ<br>(Фильтрчан)': 'lauter-tun',
            'СУСЛОВАРОЧНЫЙ АППАРАТ': 'whirlpool',
            'СУСЛОВАРОЧНЫЙ АППАРАТ (Вирпул)': 'whirlpool',
            'ГИДРОЦИКЛОННЫЙ АППАРАТ': 'hydrocyclone',
            'ГИДРОЦИКЛОННЫЙ АППАРАТ<br>(Вихревой отстойник)': 'hydrocyclone',
            'БАК ГОРЯЧЕЙ ВОДЫ': 'hot-water-tank',
            'ЦКТ (Цилиндро-конический танк)': 'fermenter',
            'ЦКТ<br>(Цилиндро-конический танк)': 'fermenter',
            'ЦКТ': 'fermenter',
            'СУСЛОСБОРНИК': 'wort-collector',
            'ФАРФАС (Ферментационная араса)': 'fermentation-vat',
            'ФАРФАС<br>(Ферментационная араса)': 'fermentation-vat',
            'ФАРФАС': 'fermentation-vat',
            'ДРОБИЛКА СОЛОДА<br>(Вальцовая)': 'malt-crusher',
            'ДРОБИЛКА СОЛОДА (Вальцовая)': 'malt-crusher',
            'ПАРОГЕНЕРАТОР<br>(Паровой котел)': 'steam-generator',
            'ПАРОГЕНЕРАТОР (Паровой котел)': 'steam-generator',
            'ЧИЛЛЕР<br>(Холодильная установка)': 'chiller',
            'ЧИЛЛЕР (Холодильная установка)': 'chiller',
            'ТЕПЛООБМЕННИК<br>(Трехконтурный, пластинчатый)': 'heat-exchanger',
            'ТЕПЛООБМЕННИК ПЛАСТИНЧАТЫЙ (Пастеризатор)': 'heat-exchanger'
        };
        
        // 3. Устанавливаем тип оборудования
        const cleanName = item.name.replace(/<br\s*\/?>/gi, ' ');
        const typeValue = nameToValueMap[item.name] || nameToValueMap[cleanName];
        
        if (typeValue) {
            const typeSelect = firstTank.querySelector('.tank-type-select');
            if (typeSelect) {
                typeSelect.value = typeValue;
                console.log(`✅ Установлен тип: ${typeValue} для "${item.name}"`);
                
                // Триггерим событие change
                const changeEvent = new Event('change', { bubbles: true });
                typeSelect.dispatchEvent(changeEvent);
            }
        }
        
        // 4. Устанавливаем объем
        const volumeInput = firstTank.querySelector('input[type="number"]');
        if (volumeInput) {
            // Извлекаем только числа из selectedVolume
            const volumeMatch = selectedVolume.match(/(\d+)/);
            const volumeNum = volumeMatch ? parseInt(volumeMatch[1]) : 1000;
            volumeInput.value = volumeNum;
            console.log(`✅ Установлен объем: ${volumeNum} л`);
        }
        
        // 5. Маппинг для автоматических опций
        const autoOptionsMap = {
            'ЗАТОРНЫЙ АППАРАТ': { 
                options: ['heating', 'mixer'], 
                heatingType: 'two_jackets',
                comment: 'Ложное днище для фильтрации, рамная мешалка'
            },
            'ЗАТОРНО-СУСЛОВАРОЧНЫЙ АППАРАТ': { 
                options: ['heating', 'mixer'], 
                heatingType: 'three_jackets',
                comment: 'Универсальный 2-в-1, усиленная конструкция для кипения'
            },
            'ФИЛЬТРАЦИОННЫЙ АППАРАТ': { 
                options: ['mixer'], 
                heatingType: null,
                comment: 'Разборное сито, система промывки дробины'
            },
            'ФИЛЬТРАЦИОННЫЙ АППАРАТ<br>(Фильтрчан)': { 
                options: ['mixer'], 
                heatingType: null,
                comment: 'Разборное сито, система промывки дробины'
            },
            'СУСЛОВАРОЧНЫЙ АППАРАТ': { 
                options: ['heating'], 
                heatingType: 'two_jackets',
                comment: 'Тангенциальный ввод для вихря, хмелевой фильтр'
            },
            'СУСЛОВАРОЧНЫЙ АППАРАТ (Вирпул)': { 
                options: ['heating'], 
                heatingType: 'two_jackets',
                comment: 'Тангенциальный ввод для вихря, хмелевой фильтр'
            },
            'ГИДРОЦИКЛОННЫЙ АППАРАТ': { 
                options: ['insulation'], 
                heatingType: null,
                comment: 'Коническая форма для центробежного разделения, термоизоляция'
            },
            'ГИДРОЦИКЛОННЫЙ АППАРАТ<br>(Вихревой отстойник)': { 
                options: ['insulation'], 
                heatingType: null,
                comment: 'Коническая форма для центробежного разделения, термоизоляция'
            },
            'БАК ГОРЯЧЕЙ ВОДЫ': { 
                options: ['heating', 'insulation'], 
                heatingType: 'two_jackets',
                comment: 'Нагрев до +95°C, термоизоляция для экономии энергии'
            },
            'ЦКТ (Цилиндро-конический танк)': { 
                options: ['cooling', 'safety_valve', 'insulation', 'cip_head'], 
                heatingType: null,
                coolingType: 'two_jackets',
                comment: 'Коническое днище для дрожжей, давление до 3 бар, CIP-мойка'
            },
            'ЦКТ<br>(Цилиндро-конический танк)': { 
                options: ['cooling', 'safety_valve', 'insulation', 'cip_head'], 
                heatingType: null,
                coolingType: 'two_jackets',
                comment: 'Коническое днище для дрожжей, давление до 3 бар, CIP-мойка'
            },
            'СУСЛОСБОРНИК': { 
                options: ['cooling', 'insulation'], 
                heatingType: null,
                coolingType: 'one_jacket',
                comment: 'Промежуточная емкость для сбора и хранения сусла'
            },
            'ФАРФАС (Ферментационная араса)': { 
                options: ['cooling', 'insulation', 'safety_valve'], 
                heatingType: null,
                coolingType: 'two_jackets',
                comment: 'Специализированная емкость для ферментации пива'
            },
            'ФАРФАС<br>(Ферментационная араса)': { 
                options: ['cooling', 'insulation', 'safety_valve'], 
                heatingType: null,
                coolingType: 'two_jackets',
                comment: 'Специализированная емкость для ферментации пива'
            }
        };
        
        // 6. Включаем автоматические опции
        const equipmentConfig = autoOptionsMap[item.name] || autoOptionsMap[cleanName];
        
        if (equipmentConfig && equipmentConfig.options) {
            console.log(`✅ Найдена конфигурация для "${item.name}": ${equipmentConfig.options.join(', ')}`);
            
            setTimeout(() => {
                // Включаем чекбоксы
                equipmentConfig.options.forEach(optionName => {
                    const checkboxName = `${optionName}_1`;
                    const checkbox = firstTank.querySelector(`[name="${checkboxName}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        console.log(`✅ Включена опция: ${checkboxName}`);
                        
                        // Триггерим событие change для показа подопций
                        const changeEvent = new Event('change', { bubbles: true });
                        checkbox.dispatchEvent(changeEvent);
                    }
                });
                
                // 7. Устанавливаем тип рубашек (если есть)
                if (equipmentConfig.heatingType) {
                    const heatingRadio = firstTank.querySelector(`input[name="heating_type_1"][value="${equipmentConfig.heatingType}"]`);
                    if (heatingRadio) {
                        heatingRadio.checked = true;
                        console.log(`✅ Установлен тип нагрева: ${equipmentConfig.heatingType}`);
                    }
                }
                
                if (equipmentConfig.coolingType) {
                    const coolingRadio = firstTank.querySelector(`input[name="cooling_type_1"][value="${equipmentConfig.coolingType}"]`);
                    if (coolingRadio) {
                        coolingRadio.checked = true;
                        console.log(`✅ Установлен тип охлаждения: ${equipmentConfig.coolingType}`);
                    }
                }
                
                // 8. Заполняем комментарий
                const commentTextarea = firstTank.querySelector('textarea[name="comment_1"]');
                if (commentTextarea) {
                    let comment = `Автоматически заполнено из каталога пивного оборудования:\n`;
                    comment += `Оборудование: ${cleanName}\n`;
                    comment += `Объем: ${selectedVolume}\n`;
                    comment += `Тип: ${typeValue || 'не указан'}\n`;
                    
                    if (equipmentConfig.comment) {
                        comment += `Описание: ${equipmentConfig.comment}`;
                    }
                    
                    commentTextarea.value = comment;
                    console.log(`✅ Комментарий заполнен`);
                }
                
            }, 300); // Задержка для гарантии
        } else {
            console.warn(`⚠️ Нет конфигурации для "${item.name}"`);
        }
        
        // 9. Заполняем основной комментарий в форме
        setTimeout(() => {
            const mainCommentField = document.querySelector('#detailed-form textarea[name="message"]');
            if (mainCommentField) {
                const comment = `=== ЗАПРОС НА РАСЧЕТ ПИВНОГО ОБОРУДОВАНИЯ ===
Оборудование: ${cleanName}
Объем: ${selectedVolume}
Дата запроса: ${new Date().toLocaleDateString('ru-RU')}

Пожалуйста, рассчитайте стоимость указанного оборудования с выбранным объемом. Укажите сроки изготовления и условия доставки.`;
                
                mainCommentField.value = comment;
                console.log(`✅ Основной комментарий формы заполнен`);
            }
        }, 500);
        
        console.log(`✅ Форма успешно заполнена для: "${item.name}"`);
    }

    function replaceTankTypesToBrewery() {
        const tankTypeSelects = document.querySelectorAll('.tank-type-select, select[name*="type"], select[name*="tank_type"]');
        
        if (tankTypeSelects.length === 0) return;
        
        const breweryTankTypes = [
            { value: 'brewing-kettle', label: 'Заторный аппарат' },
            { value: 'brewhouse', label: 'Заторно-сусловарочный аппарат' },
            { value: 'lauter-tun', label: 'Фильтрационный аппарат' },
            { value: 'whirlpool', label: 'Сусловарочный аппарат (Вирпул)' },
            { value: 'hydrocyclone', label: 'Гидроциклонный аппарат' },
            { value: 'hot-water-tank', label: 'Бак горячей воды' },
            { value: 'fermenter', label: 'ЦКТ (Цилиндро-конический танк)' },
            { value: 'wort-collector', label: 'Суслосборник' },
            { value: 'fermentation-vat', label: 'ФАРФАС (Ферментационная араса)' }
        ];
        
        tankTypeSelects.forEach((select, index) => {
            const currentValue = select.value;
            select.innerHTML = '';
            
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Выберите тип оборудования';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            select.appendChild(defaultOption);
            
            breweryTankTypes.forEach(tankType => {
                const option = document.createElement('option');
                option.value = tankType.value;
                option.textContent = tankType.label;
                select.appendChild(option);
            });
            
            if (currentValue && breweryTankTypes.some(t => t.value === currentValue)) {
                select.value = currentValue;
            }
            
            const changeEvent = new Event('change', { bubbles: true });
            select.dispatchEvent(changeEvent);
        });
    }

    function showSuccessNotification(itemName, selectedVolume) {
        const notification = document.createElement('div');
        notification.className = 'equipment-notification';
        notification.innerHTML = `
            <div style="position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #00a86b, #00bf6f); color: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); z-index: 9999; max-width: 350px; animation: slideInRight 0.5s ease; border-left: 5px solid #008055;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%; 
                         display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        ✅
                    </div>
                    <div style="font-weight: 600; font-size: 1.1rem;">Форма заполнена!</div>
                </div>
                <div style="font-size: 0.95rem; opacity: 0.95; line-height: 1.5;">
                    <div style="margin-bottom: 5px;"><strong>${itemName}</strong></div>
                    <div>Параметры: ${selectedVolume}</div>
                    <div style="margin-top: 8px; font-size: 0.85rem;">Все поля установлены автоматически</div>
                </div>
            </div>
        `;
        
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
                @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
                .equipment-notification { animation: slideInRight 0.5s ease; }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.5s ease forwards';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 5000);
    }

    // Функция для быстрой заявки
    function openQuickRequest(itemName, selectedVolume) {
        const quickForm = document.getElementById('quick-form');
        const quickTabBtn = document.querySelector('[data-tab="quick"]');
        
        if (quickForm && quickTabBtn) {
            const allTabs = document.querySelectorAll('.tab-btn');
            const allForms = document.querySelectorAll('.tab-content');
            
            allTabs.forEach(tab => tab.classList.remove('active'));
            allForms.forEach(form => {
                form.classList.remove('active');
                form.style.display = 'none';
            });
            
            quickTabBtn.classList.add('active');
            quickForm.classList.add('active');
            quickForm.style.display = 'block';
            
            setTimeout(() => {
                const commentText = `=== ЗАПРОС НА ВСПОМОГАТЕЛЬНОЕ ОБОРУДОВАНИЕ ===
Тип: ${itemName}
Параметры: ${selectedVolume}
Дата запроса: ${new Date().toLocaleDateString('ru-RU')}
---
Требуется расчет вспомогательного оборудования.
Прошу предоставить коммерческое предложение и сроки поставки.`;
                
                const quickCommentField = document.querySelector('#quick-form textarea[name="message"]');
                if (quickCommentField) {
                    quickCommentField.value = commentText;
                    setTimeout(() => {
                        quickCommentField.focus();
                        quickCommentField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            }, 300);
            
            setTimeout(() => {
                if (quickForm) {
                    quickForm.style.scrollMarginTop = "50px";
                    quickForm.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }, 500);
        }
    }

    // Функция calculateEquipment
    function calculateEquipment(itemId, itemName) {
        console.log('🍺 Рассчитываем оборудование:', itemName);
        
        const selectedVolume = window.selectedVolumes[itemId] || beerEquipmentData.equipment.find(eq => eq.id === itemId).defaultVolume;
        
        // Закрываем модальное окно
        closeEquipmentModal(itemId);
        
        // Определяем тип оборудования по ID
        const isAuxEquipment = itemId.startsWith('aux-');
        
        if (isAuxEquipment) {
            console.log('Переход на быструю заявку для:', itemName);
            openQuickRequest(itemName, selectedVolume);
        } else {
            console.log('Переход на подробную форму для:', itemName);
            openDetailedFormWithData(itemName, selectedVolume, itemId);
        }
    }

    // Функция переключения категорий
    function switchCategory(category) {
        // Обновляем активную кнопку
        const tabs = document.querySelectorAll('.category-tab');
        tabs.forEach(tab => {
            if (tab.dataset.category === category) {
                tab.style.background = 'linear-gradient(135deg, #2b2b39, #2c3e50)';
                tab.style.color = 'white';
                tab.style.boxShadow = '0 6px 20px rgba(44, 82, 130, 0.3)';
                tab.classList.add('active');
            } else {
                tab.style.background = '#f8fafc';
                tab.style.color = '#4a5568';
                tab.style.boxShadow = 'none';
                tab.classList.remove('active');
            }
        });
        
        // Показываем/скрываем контентные блоки
        const contents = document.querySelectorAll('.category-content');
        contents.forEach(content => {
            if (content.id === `${category}-content`) {
                content.style.display = 'block';
                setTimeout(() => {
                    content.style.opacity = '1';
                }, 10);
            } else {
                content.style.display = 'none';
                content.style.opacity = '0';
            }
        });
        
        const element = document.getElementById(`${category}-content`);
    const yOffset = -300; // смещение в пикселях (отрицательное = выше)
    const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
    
    window.scrollTo({
        top: y,
        behavior: 'smooth'
    });
    }

    // Инициализация каталога
    function initEquipmentCatalog() {
        // Инициализация состояний картинок
        window.imageStates = {};
        
        // Фильтруем оборудование по категориям
        const brewingEquipment = beerEquipmentData.equipment.filter(item => item.subcategory === 'brewing');
        const fermentationEquipment = beerEquipmentData.equipment.filter(item => item.subcategory === 'fermentation');
        const auxiliaryEquipment = beerEquipmentData.equipment.filter(item => item.subcategory === 'auxiliary');
        
        // Инициализируем контейнеры
        const brewingContainer = document.getElementById('brewing-equipment-container');
        const fermentationContainer = document.getElementById('fermentation-equipment-container');
        const auxiliaryContainer = document.getElementById('auxiliary-equipment-container');
        
        // Очищаем контейнеры
        if (brewingContainer) brewingContainer.innerHTML = '';
        if (fermentationContainer) fermentationContainer.innerHTML = '';
        if (auxiliaryContainer) auxiliaryContainer.innerHTML = '';
        
        // Создаем карточки для варочного порядка
        brewingEquipment.forEach(item => {
            const card = createEquipmentCard(item);
            if (brewingContainer) brewingContainer.appendChild(card);
        });
        
        // Создаем карточки для бродильного цеха
        fermentationEquipment.forEach(item => {
            const card = createEquipmentCard(item);
            if (fermentationContainer) fermentationContainer.appendChild(card);
        });
        
        // Создаем карточки для вспомогательного оборудования
        auxiliaryEquipment.forEach(item => {
            const card = createEquipmentCard(item);
            if (auxiliaryContainer) auxiliaryContainer.appendChild(card);
        });
    }

    // Функция создания карточки оборудования
    function createEquipmentCard(item) {
        const card = document.createElement('div');
        card.className = 'equipment-card';
        card.setAttribute('data-item-id', item.id);
        card.setAttribute('data-category', item.subcategory);
        card.style.cssText = `background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; flex-direction: column; height: 100%; min-height: 520px; position: relative;`;
        
        card.onmouseenter = () => {
            card.style.transform = 'translateY(-10px) scale(1.02)';
            card.style.boxShadow = '0 20px 40px rgba(0,0,0,0.12)';
        };
        card.onmouseleave = () => {
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
        };
        
        card.innerHTML = createShortCard(item);
        
        return card;
    }

// Функция выбора объема
function selectVolume(itemId, volume) {
    console.log('Выбран объем:', volume, 'для оборудования:', itemId);
    
    // Создаем объект если его нет
    if (typeof window.selectedVolumes === 'undefined') {
        window.selectedVolumes = {};
    }
    
    // Сохраняем выбранный объем
    window.selectedVolumes[itemId] = volume;
    
    // Находим модальное окно
    const modal = document.getElementById(`equipment-modal-${itemId}`);
    if (!modal) {
        console.error('Модальное окно не найдено');
        return;
    }
    
    // Находим все кнопки объема
    const volumeButtons = modal.querySelectorAll('.volume-btn');
    console.log('Найдено кнопок:', volumeButtons.length);
    
    // Меняем стиль кнопок
    volumeButtons.forEach(btn => {
        const btnVolume = btn.getAttribute('data-volume');
        if (btnVolume === volume) {
            btn.style.borderColor = '#f77c2a';
            btn.style.backgroundColor = '#fff5eb';
            btn.style.color = '#f77c2a';
            btn.style.fontWeight = '600';
            btn.classList.add('selected');
        } else {
            btn.style.borderColor = '#e2e8f0';
            btn.style.backgroundColor = 'white';
            btn.style.color = '#4a5568';
            btn.style.fontWeight = '400';
            btn.classList.remove('selected');
        }
    });
}

    // Обработчики событий
    document.addEventListener('DOMContentLoaded', function() {
        initEquipmentCatalog();
        
        // Инициализируем активную категорию (варочный порядок)
        switchCategory('brewing');
        
        // Обработчики для модальных окон
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('equipment-modal')) {
                const modalId = event.target.id;
                const itemId = modalId.replace('equipment-modal-', '');
                closeEquipmentModal(itemId);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const openModals = document.querySelectorAll('.equipment-modal[style*="display: flex"]');
                openModals.forEach(modal => {
                    const itemId = modal.id.replace('equipment-modal-', '');
                    closeEquipmentModal(itemId);
                });
            }
        });

        // Проверка URL для открытия модального окна
        const urlParams = new URLSearchParams(window.location.search);
        const equipmentId = urlParams.get('equipment');
        
        if (equipmentId) {
            setTimeout(function() {
                if (typeof showEquipmentModal === 'function') {
                    showEquipmentModal(equipmentId);
                }
            }, 1000);
        }
    });
    // Принудительно показываем блоки
window.addEventListener('load', function() {
    setTimeout(function() {
        const sections = [
            '.brewery-section',
            '.brewery-projects-section',
            '.brewery-config-content',
            '.equipment-catalog-section',
            '.equipment-configuration-section'
        ];
        
        sections.forEach(selector => {
            const element = document.querySelector(selector);
            if (element) {
                element.style.display = 'block';
                element.style.visibility = 'visible';
                element.style.opacity = '1';
                element.style.height = 'auto';
                element.style.overflow = 'visible';
            }
        });
    }, 100);
});

// ВКЛЮЧЕНИЕ БЛОКОВ КОМПЛЕКТАЦИЙ
document.addEventListener('DOMContentLoaded', function() {
    console.log('Активируем блоки комплектаций...');
    
    // 1. Сначала скрываем ВСЕ блоки
    const infoSections = document.querySelectorAll('.config-info-section');
    infoSections.forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none';
        section.style.opacity = '0';
        section.style.visibility = 'hidden';
        section.style.height = '0';
        section.style.overflow = 'hidden';
    });
    
    // 2. Показываем ТОЛЬКО первый блок
    const firstSection = document.getElementById('lot1-info');
    if (firstSection) {
        firstSection.classList.add('active');
        firstSection.style.display = 'block';
        firstSection.style.opacity = '1';
        firstSection.style.visibility = 'visible';
        firstSection.style.height = 'auto';
        firstSection.style.overflow = 'visible';
    }
    
    // 3. Устанавливаем активную кнопку
    const firstButton = document.querySelector('.config-btn[data-config="lot1"]');
    if (firstButton) {
        firstButton.classList.add('active');
    }
    
    // 4. Добавляем обработчики кликов на кнопки
    document.querySelectorAll('.config-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const config = this.getAttribute('data-config');
            console.log('Выбрана комплектация:', config);
            
            // Убираем активный класс со всех кнопок
            document.querySelectorAll('.config-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Добавляем активный класс к нажатой кнопке
            this.classList.add('active');
            
            // Скрываем все блоки информации
            infoSections.forEach(section => {
                section.classList.remove('active');
                section.style.display = 'none';
                section.style.opacity = '0';
                section.style.visibility = 'hidden';
                section.style.height = '0';
                section.style.overflow = 'hidden';
            });
            
            // Показываем выбранный блок
            const targetSection = document.getElementById(config + '-info');
            if (targetSection) {
                targetSection.classList.add('active');
                targetSection.style.display = 'block';
                setTimeout(() => {
                    targetSection.style.opacity = '1';
                    targetSection.style.visibility = 'visible';
                    targetSection.style.height = 'auto';
                    targetSection.style.overflow = 'visible';
                    
                    // Плавная прокрутка к блоку
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 10);
            }
        });
    });
    
    // 5. Показываем секцию конфигураций
    const configSection = document.querySelector('.equipment-configuration-section');
    if (configSection) {
        configSection.style.display = 'block';
        configSection.style.opacity = '1';
        configSection.style.visibility = 'visible';
    }
    
    console.log(`✅ Настроено ${infoSections.length} блоков комплектаций`);
});

// popstate для модалок пивного оборудования
window.addEventListener('popstate', function() {
    var match = window.location.search.match(/[?&]equipment=([^&]+)/);
    if (match) {
        if (typeof showEquipmentModal === 'function') {
            showEquipmentModal(match[1]);
        }
    } else {
        // Закрываем все открытые модалки
        document.querySelectorAll('[id^="equipment-modal-"]').forEach(function(m) {
            if (m.style.display === 'flex') {
                var id = m.id.replace('equipment-modal-', '');
                if (typeof closeEquipmentModal === 'function') {
                    closeEquipmentModal(id);
                }
            }
        });
    }
});
