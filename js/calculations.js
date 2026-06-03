// ====================================================
// ФАЙЛ: calculations.js
// ВСЕ ФУНКЦИИ И ДАННЫЕ ДЛЯ РАСЧЁТОВ ОБОРУДОВАНИЯ
// ====================================================

console.log("📊 Загружен модуль расчётов");

// ===== ДАННЫЕ ДЛЯ БЫСТРОЙ ЗАЯВКИ =====
const quickIndustryData = {
  beer: {
    defaultPrices: { 250: 200000, 500: 350000, 1000: 600000, 3000: 1200000, 5000: 2000000, 10000: 3500000 },
    types: [
      { value: 'mash_tun', label: 'Заторный аппарат', prices: { 250: 273000, 500: 403000, 1000: 637000, 3000: 1400000, 5000: 2000000, 10000: 3500000 } },
      { value: 'combined_kettle', label: 'Заторно-сусловарочный аппарат', prices: { 250: 350000, 500: 520000, 1000: 800000, 3000: 1800000, 5000: 2600000, 10000: 4500000 } },
      { value: 'lauter_tun', label: 'Фильтрационный аппарат (Фильтрчан)', prices: { 250: 280000, 500: 420000, 1000: 650000, 3000: 1450000, 5000: 2100000, 10000: 3600000 } },
      { value: 'brew_kettle', label: 'Сусловарочный аппарат', prices: { 250: 320000, 500: 480000, 1000: 750000, 3000: 1600000, 5000: 2400000, 10000: 4000000 } },
      { value: 'whirlpool', label: 'Гидроциклонный аппарат (Вихревой отстойник)', prices: { 250: 220000, 500: 330000, 1000: 520000, 3000: 1150000, 5000: 1700000, 10000: 2900000 } },
      { value: 'hot_water', label: 'Бак горячей воды', prices: { 250: 150000, 500: 189000, 1000: 286000, 3000: 624000, 5000: 936000, 10000: 1600000 } },
      { value: 'wort_receiver', label: 'Суслосборник', prices: { 250: 180000, 500: 270000, 1000: 420000, 3000: 950000, 5000: 1400000, 10000: 2400000 } },
      { value: 'ckt', label: 'ЦКТ (Цилиндро-конический танк)', prices: { 250: 94000, 500: 150000, 1000: 247000, 3000: 598000, 5000: 936000, 10000: 1800000 } },
      { value: 'forfas', label: 'Форфасы', prices: { 250: 111000, 500: 169000, 1000: 273000, 3000: 624000, 5000: 936000, 10000: 1800000 } },
      { value: 'mill', label: 'Дробилка солода' },
      { value: 'steam', label: 'Парогенератор' },
      { value: 'chiller', label: 'Чиллер' },
      { value: 'heatex', label: 'Теплообменник' }
    ]
  },
  dairy: {
    defaultPrices: { 250: 250000, 500: 450000, 1000: 750000, 3000: 1800000, 5000: 2800000, 10000: 4800000 },
    types: [
      { value: 'reception', label: 'Ёмкость приёмки молока (ПОМ)', prices: { 1000: 195000, 3000: 460000, 5000: 684000, 10000: 1200000 } },
      { value: 'storage', label: 'Резервуар для хранения молока', prices: { 3000: 367000, 5000: 546000, 10000: 937000 } },
      { value: 'cheesemaker', label: 'Сыроизготовитель', prices: { 500: 306000, 1000: 525000, 3000: 1200000, 5000: 1800000, 10000: 3200000 } },
      { value: 'fermentation', label: 'Ферментационный танк', prices: { 500: 247000, 1000: 424000, 3000: 999000, 5000: 1500000, 10000: 2600000 } },
      { value: 'pasteurizer', label: 'Ванна пастеризации (ВДП)', prices: { 500: 345000, 1000: 593000, 3000: 1400000, 5000: 2100000, 10000: 3600000 } },
      { value: 'curd', label: 'Творогоизготовитель', prices: { 500: 350000, 1000: 600000, 3000: 1400000, 5000: 2200000, 10000: 3800000 } },
      { value: 'shelf', label: 'Стеллажная камера созревания' }
    ]
  },
  wine: {
    defaultPrices: { 250: 200000, 500: 350000, 1000: 600000, 3000: 1400000, 5000: 2200000, 10000: 3800000 },
    types: [
      { value: 'red_fermenter', label: 'Ферментационная ёмкость для красных вин', prices: { 500: 280000, 1000: 480000, 3000: 1100000, 5000: 1700000, 10000: 2900000 } },
      { value: 'white_fermenter', label: 'Ферментационный танк для белых вин', prices: { 500: 300000, 1000: 500000, 3000: 1200000, 5000: 1800000, 10000: 3100000 } },
      { value: 'aging', label: 'Цистерна для выдержки и хранения вина', prices: { 1000: 350000, 3000: 750000, 5000: 1100000, 10000: 2000000 } },
      { value: 'cryostat', label: 'Танк холодной стабилизации (Криостат)', prices: { 500: 350000, 1000: 600000, 3000: 1300000, 5000: 2000000, 10000: 3400000 } },
      { value: 'blending', label: 'Емкость для купажирования', prices: { 1000: 450000, 3000: 1000000, 5000: 1500000, 10000: 2600000 } },
      { value: 'universal', label: 'Винификатор', prices: { 500: 380000, 1000: 650000, 3000: 1500000, 5000: 2300000, 10000: 3800000 } }
    ]
  },
  cheese: {
    defaultPrices: { 250: 200000, 500: 350000, 1000: 650000, 3000: 1500000, 5000: 2500000, 10000: 4000000 },
    types: [
      { value: 'cheesemaker', label: 'Сыроизготовитель' },
      { value: 'pasteurizer_bath', label: 'Ванна пастеризации' },
      { value: 'chamber', label: 'Камера созревания' },
      { value: 'salt_bath', label: 'Солевая ванна' },
      { value: 'cheese_press', label: 'Пресс для сыра' }
    ]
  },
  juice: {
    defaultPrices: { 250: 180000, 500: 320000, 1000: 600000, 3000: 1400000, 5000: 2200000, 10000: 3800000 },
    types: [
      { value: 'boiler', label: 'Варочный котел' },
      { value: 'pasteurizer', label: 'Пастеризатор' },
      { value: 'storage', label: 'Емкость хранения' }
    ]
  },
  oil: {
    defaultPrices: { 250: 220000, 500: 400000, 1000: 750000, 3000: 1800000, 5000: 2900000, 10000: 5000000 },
    types: [
      { value: 'storage', label: 'Емкость хранения' }
    ]
  },
  other: {
    defaultPrices: { 250: 200000, 500: 350000, 1000: 600000, 3000: 1500000, 5000: 2400000, 10000: 4000000 },
    types: [
      { value: 'storage', label: 'Резервуар для хранения', prices: { 1000: 144000, 3000: 341000, 5000: 507000, 10000: 870000 } },
      { value: 'mixing', label: 'Ёмкость с мешалкой', prices: { 500: 224000, 1000: 384000, 3000: 904000, 5000: 1300000, 10000: 2300000 } },
      { value: 'thermal', label: 'Ёмкость с терморегуляцией', prices: { 500: 198000, 1000: 341000, 3000: 803000, 5000: 1200000, 10000: 2100000 } },
      { value: 'pressure', label: 'Емкость под давлением', prices: { 500: 216000, 1000: 371000, 3000: 874000, 5000: 1300000, 10000: 2200000 } },
      { value: 'cip', label: 'CIP-станция' },
      { value: 'heatex', label: 'Теплообменник пластинчатый / кожухотрубный' }
    ]
  }
};

function updateQuickTypes() {
  const industry = document.getElementById('quick-industry').value;
  const typeSelect = document.getElementById('quick-type');
  typeSelect.innerHTML = '';
  if (!industry) {
    typeSelect.innerHTML = '<option value="">-- Любой тип --</option>';
    updateQuickPrice();
    return;
  }
  const data = quickIndustryData[industry];
  if (!data) {
    typeSelect.innerHTML = '<option value="">-- Любой тип --</option>';
    updateQuickPrice();
    return;
  }
  typeSelect.innerHTML = '<option value="">-- Выберите тип --</option>';
  data.types.forEach(t => {
    const opt = document.createElement('option');
    opt.value = t.value; opt.textContent = t.label;
    typeSelect.appendChild(opt);
  });
  updateQuickPrice();
}

function selectQuickVolume(vol) {
  document.querySelectorAll('.quick-vol-btn').forEach(b => b.classList.remove('active'));
  const customInput = document.getElementById('quick-volume-custom');
  const hiddenInput = document.getElementById('quick-volume');
  if (vol === 'custom') {
    document.querySelector('.quick-vol-btn.custom').classList.add('active');
    customInput.style.display = 'block';
    customInput.focus();
    hiddenInput.value = '';
  } else {
    document.querySelector(`.quick-vol-btn[data-vol="${vol}"]`).classList.add('active');
    customInput.style.display = 'none';
    customInput.value = '';
    hiddenInput.value = vol;
  }
  updateQuickPrice();
}

function updateQuickPrice() {
  const industry = document.getElementById('quick-industry').value;
  const priceValue = document.getElementById('quick-price-value');
  const customInput = document.getElementById('quick-volume-custom');
  const hiddenInput = document.getElementById('quick-volume');

  const data = quickIndustryData[industry];
  if (!data) { priceValue.textContent = 'По запросу'; return; }

  const typeVal = document.getElementById('quick-type').value;
  let typeObj = null;
  for (let x = 0; x < data.types.length; x++) {
    if (data.types[x].value === typeVal) { typeObj = data.types[x]; break; }
  }
  const prices = (typeObj && typeObj.prices) || data.defaultPrices;
  if (!prices) { priceValue.textContent = 'По запросу'; return; }

  let vol = parseInt(hiddenInput.value);
  if (customInput.style.display !== 'none' && customInput.value) {
    vol = parseInt(customInput.value);
  }

  const sortedVolumes = Object.keys(prices).map(Number).sort((a,b) => a - b);
  if (!vol || vol <= 0) {
    priceValue.textContent = 'от ' + prices[sortedVolumes[0]].toLocaleString('ru-RU') + ' ₽';
    return;
  }

  let price = null;
  for (const sv of sortedVolumes) {
    if (vol <= sv) { price = prices[sv]; break; }
  }
  if (price === null) {
    const maxVol = sortedVolumes[sortedVolumes.length - 1];
    price = prices[maxVol] * (vol / maxVol);
  }

  priceValue.textContent = 'от ' + Math.round(price).toLocaleString('ru-RU') + ' ₽';
}

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

// === ОСНОВНЫЕ ФУНКЦИИ РАСЧЁТОВ ===

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

// ФУНКЦИЯ ДЛЯ МОЛОЧНОЙ ФОРМЫ
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
        showFieldError(nameField, 'Введите имя');
        return false;
    }
    
    if (!phoneField || !phoneField.value.trim()) {
        showFieldError(phoneField, 'Введите телефон');
        return false;
    }
    
    if (!privacyCheckbox || !privacyCheckbox.checked) {
        showFieldError(privacyCheckbox, 'Примите политику конфиденциальности');
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
                showFieldError(nameField, 'Введите имя');
                e.preventDefault();
                return false;
            }
            
            if (!phoneField || !phoneField.value.trim()) {
                showFieldError(phoneField, 'Введите телефон');
                e.preventDefault();
                return false;
            }
            
            if (!checkbox || !checkbox.checked) {
                showFieldError(checkbox, 'Примите политику конфиденциальности');
                e.preventDefault();
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

// ЦЕЛИ ДЛЯ ФОРМ ЗАЯВОК (Яндекс.Метрика ID: 109477134)
// Основная функция отправки целей
function trackYandexMetricaGoal(goalName) {
    if (typeof ym !== 'undefined') {
        ym(109477134, 'reachGoal', goalName);
    }
}

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

// ====================================================
// ЭКСПОРТ ВСЕХ ФУНКЦИЙ В ГЛОБАЛЬНУЮ ОБЛАСТЬ ВИДИМОСТИ
// ====================================================

window.scrollToDairyCalculation = scrollToDairyCalculation;
window.scrollToWineryCalculation = scrollToWineryCalculation;
window.activateTab = activateTab;
window.loadBreweryLotData = loadBreweryLotData;
window.addAdditionalCKT = addAdditionalCKT;
window.addTankCard = addTankCard;
window.updateSelectedSummary = updateSelectedSummary;
window.handleWineryTypeChange = handleWineryTypeChange;
window.addWineryEquipmentCard = addWineryEquipmentCard;
window.handleDairyTypeChange = handleDairyTypeChange;
window.addDairyEquipmentCard = addDairyEquipmentCard;
window.prepareBreweryData = prepareBreweryData;
window.handleBrewerySubmit = handleBrewerySubmit;
window.setupBreweryForm = setupBreweryForm;
window.setupWineryForm = setupWineryForm;
window.scrollToTankType = scrollToTankType;
window.scrollToBreweryTab = scrollToBreweryTab;
window.scrollToDairyWithShelves = scrollToDairyWithShelves;
window.scrollToBreweryLot = scrollToBreweryLot;
window.trackYandexMetricaGoal = trackYandexMetricaGoal;
window.scrollToBreweryLotOnClick = scrollToBreweryLotOnClick;
window.initBreweryConfigButtons = initBreweryConfigButtons;

console.log("✅ Модуль расчётов загружен и готов к работе");

// ====================================================
// ФУНКЦИИ ДЛЯ ПОДОПЦИЙ НАГРЕВА/ОХЛАЖДЕНИЯ/АРМАТУРЫ
// ====================================================

function initSuboptions() {
    console.log('🔧 Инициализация подопций...');
    
    // Для каждой емкости на странице
    document.querySelectorAll('.tank-configurator').forEach(tank => {
        const tankId = tank.id.replace('tank-', '') || tank.getAttribute('data-tank-number') || '1';
        
        // Находим чекбоксы
        const heatingCheckbox = tank.querySelector('input[name="heating_' + tankId + '"]');
        const coolingCheckbox = tank.querySelector('input[name="cooling_' + tankId + '"]');
        const valvesCheckbox = tank.querySelector('input[name="valves_' + tankId + '"]');
        
        // Нагрев
        if (heatingCheckbox) {
            heatingCheckbox.addEventListener('change', function() {
                const suboptionsId = 'heating-sub-' + tankId;
                const suboptionsDiv = document.getElementById(suboptionsId);
                if (suboptionsDiv) {
                    suboptionsDiv.style.display = this.checked ? 'block' : 'none';
                    
                    if (!this.checked) {
                        // Сбрасываем выбор
                        tank.querySelectorAll('input[name="heating_type_' + tankId + '"]').forEach(radio => {
                            radio.checked = false;
                        });
                    }
                }
            });
            
            // Показываем сразу если уже отмечено
            if (heatingCheckbox.checked) {
                const suboptionsDiv = document.getElementById('heating-sub-' + tankId);
                if (suboptionsDiv) suboptionsDiv.style.display = 'block';
            }
        }
        
        // Охлаждение
        if (coolingCheckbox) {
            coolingCheckbox.addEventListener('change', function() {
                const suboptionsId = 'cooling-sub-' + tankId;
                const suboptionsDiv = document.getElementById(suboptionsId);
                if (suboptionsDiv) {
                    suboptionsDiv.style.display = this.checked ? 'block' : 'none';
                    
                    if (!this.checked) {
                        tank.querySelectorAll('input[name="cooling_type_' + tankId + '"]').forEach(radio => {
                            radio.checked = false;
                        });
                    }
                }
            });
            
            if (coolingCheckbox.checked) {
                const suboptionsDiv = document.getElementById('cooling-sub-' + tankId);
                if (suboptionsDiv) suboptionsDiv.style.display = 'block';
            }
        }
        
        // Запорная арматура
        if (valvesCheckbox) {
            valvesCheckbox.addEventListener('change', function() {
                const suboptionsId = 'valves-sub-' + tankId;
                const suboptionsDiv = document.getElementById(suboptionsId);
                if (suboptionsDiv) {
                    suboptionsDiv.style.display = this.checked ? 'block' : 'none';
                    
                    if (!this.checked) {
                        const manualValves = tank.querySelector('input[name="manual_valves_' + tankId + '"]');
                        const pneumaticValves = tank.querySelector('input[name="pneumatic_valves_' + tankId + '"]');
                        
                        if (manualValves) manualValves.checked = false;
                        if (pneumaticValves) pneumaticValves.checked = false;
                    }
                }
            });
            
            if (valvesCheckbox.checked) {
                const suboptionsDiv = document.getElementById('valves-sub-' + tankId);
                if (suboptionsDiv) suboptionsDiv.style.display = 'block';
            }
        }
    });
    
    console.log('✅ Подопции инициализированы');
}

// Функция для конкретной емкости (для динамического добавления)
function initSuboptionsForTank(tankElement, tankNumber) {
    const heatingCheckbox = tankElement.querySelector('input[name="heating_' + tankNumber + '"]');
    const coolingCheckbox = tankElement.querySelector('input[name="cooling_' + tankNumber + '"]');
    const valvesCheckbox = tankElement.querySelector('input[name="valves_' + tankNumber + '"]');
    
    // Нагрев
    if (heatingCheckbox) {
        heatingCheckbox.addEventListener('change', function() {
            const suboptionsId = 'heating-sub-' + tankNumber;
            const suboptionsDiv = document.getElementById(suboptionsId);
            if (suboptionsDiv) {
                suboptionsDiv.style.display = this.checked ? 'block' : 'none';
                
                if (!this.checked) {
                    tankElement.querySelectorAll('input[name="heating_type_' + tankNumber + '"]').forEach(radio => {
                        radio.checked = false;
                    });
                }
            }
        });
    }
    
    // Охлаждение
    if (coolingCheckbox) {
        coolingCheckbox.addEventListener('change', function() {
            const suboptionsId = 'cooling-sub-' + tankNumber;
            const suboptionsDiv = document.getElementById(suboptionsId);
            if (suboptionsDiv) {
                suboptionsDiv.style.display = this.checked ? 'block' : 'none';
                
                if (!this.checked) {
                    tankElement.querySelectorAll('input[name="cooling_type_' + tankNumber + '"]').forEach(radio => {
                        radio.checked = false;
                    });
                }
            }
        });
    }
    
    // Запорная арматура
    if (valvesCheckbox) {
        valvesCheckbox.addEventListener('change', function() {
            const suboptionsId = 'valves-sub-' + tankNumber;
            const suboptionsDiv = document.getElementById(suboptionsId);
            if (suboptionsDiv) {
                suboptionsDiv.style.display = this.checked ? 'block' : 'none';
                
                if (!this.checked) {
                    const manualValves = tankElement.querySelector('input[name="manual_valves_' + tankNumber + '"]');
                    const pneumaticValves = tankElement.querySelector('input[name="pneumatic_valves_' + tankNumber + '"]');
                    
                    if (manualValves) manualValves.checked = false;
                    if (pneumaticValves) pneumaticValves.checked = false;
                }
            }
        });
    }
}

// Обновленная функция добавления емкости
function addTankCardWithSuboptions() {
    console.log('➕ Добавление новой емкости с подопциями...');
    
    const template = document.getElementById('detailed-tank-template');
    const container = document.querySelector('#detailed-form .configurator-container');
    
    if (!template || !container) {
        alert('Не удалось добавить емкость. Обновите страницу.');
        return;
    }
    
    const existingTanks = container.querySelectorAll('.tank-configurator.detailed-tank, #tank-1');
    const newTankNumber = existingTanks.length + 1;
    
    // Клонируем и заменяем
    const templateContent = template.innerHTML;
    const newTankHTML = templateContent
        .replace(/data-tank-number="X"/g, `data-tank-number="${newTankNumber}"`)
        .replace(/<h4>Емкость X<\/h4>/g, `<h4>Емкость ${newTankNumber}</h4>`)
        .replace(/name="([^"]+)_X"/g, `name="$1_${newTankNumber}"`)
        .replace(/id="([^"]+)-X"/g, `id="$1-${newTankNumber}"`)
        .replace(/data-suboptions="([^"]+)-X"/g, `data-suboptions="$1-${newTankNumber}"`);
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = newTankHTML;
    const tankElement = tempDiv.firstElementChild;
    
    if (!tankElement) return;
    
    tankElement.id = `tank-${newTankNumber}`;
    
    // Кнопка удаления
    const removeBtn = tankElement.querySelector('.remove-equipment-btn');
    if (removeBtn) {
        removeBtn.onclick = function() {
            if (confirm('Удалить эту емкость?')) {
                tankElement.remove();
                updateAllTankNumbers();
            }
        };
    }
    
    // Добавляем
    const addButton = container.querySelector('.add-tank-btn');
    if (addButton) {
        container.insertBefore(tankElement, addButton);
    } else {
        container.appendChild(tankElement);
    }
    
    // Инициализируем подопции
    setTimeout(() => {
        initSuboptionsForTank(tankElement, newTankNumber);
    }, 100);
    
    console.log(`✅ Добавлена емкость #${newTankNumber}`);
}

// Обновляем существующую функцию
function addTankCard() {
    console.log('➕ Добавление новой емкости (обновленная версия)...');
    addTankCardWithSuboptions();
}

// Инициализируем при загрузке
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initSuboptions();
    }, 500);
});

// Экспортируем
window.initSuboptions = initSuboptions;
window.initSuboptionsForTank = initSuboptionsForTank;
window.addTankCardWithSuboptions = addTankCardWithSuboptions;

console.log("✅ Модуль расчётов загружен и готов к работе");

// Автоматическая инициализация при загрузке
(function() {
    console.log('📋 Автоматическая инициализация формы...');
    
    // Ждем полной загрузки страницы
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        // DOM уже загружен
        setTimeout(initForm, 1000);
    }
    
    function initForm() {
        if (typeof initSuboptions === 'function') {
            initSuboptions();
        }
        
        // Активируем подопции для первой емкости если нужно
        const firstHeating = document.querySelector('input[name="heating_1"]');
        const firstCooling = document.querySelector('input[name="cooling_1"]');
        const firstValves = document.querySelector('input[name="valves_1"]');
        
        if (firstHeating && firstHeating.checked) {
            const subDiv = document.getElementById('heating-sub-1');
            if (subDiv) subDiv.style.display = 'block';
        }
        
        if (firstCooling && firstCooling.checked) {
            const subDiv = document.getElementById('cooling-sub-1');
            if (subDiv) subDiv.style.display = 'block';
        }
        
        if (firstValves && firstValves.checked) {
            const subDiv = document.getElementById('valves-sub-1');
            if (subDiv) subDiv.style.display = 'block';
        }
        
        console.log('✅ Форма инициализирована');
    }
})();
