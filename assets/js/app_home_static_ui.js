/**
 * Static home UI templates for eFactor App Home Page studio.
 * Complete dense pages (no cube rotation). Does not alter schema structure.
 */
(function (global) {
  'use strict';

  /**
   * The bottom navigation is identical for every user and every template:
   * Home, IoT, Grid, Profile. Templates must not restyle, reorder, or replace Grid.
   */
  var CANONICAL_BOTTOM_NAV = [
    { id: 'home', icon: 'fa-home', label: 'Home', active: true },
    { id: 'devices', icon: 'fa-th', label: 'IoT', active: false },
    { id: 'grid', icon: 'fa-border-all', label: 'Grid', active: false },
    { id: 'profile', icon: 'fa-user', label: 'Profile', active: false }
  ];

  /** Fresh copy per template so a caller mutating one nav can't affect the others. */
  function canonicalBottomNav() {
    return CANONICAL_BOTTOM_NAV.map(function (item) {
      return Object.assign({}, item);
    });
  }

  var IMG = {
    food: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=640&h=420&fit=crop',
    food2: 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=640&h=420&fit=crop',
    fashion: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=480&h=640&fit=crop',
    fashion2: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=480&h=640&fit=crop',
    fashion3: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=480&h=640&fit=crop',
    grocery: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop',
    grocery2: 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?w=400&h=400&fit=crop',
    home: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=480&h=480&fit=crop',
    home2: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=480&h=480&fit=crop',
    beauty: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
    beauty2: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
    delivery: 'https://images.unsplash.com/photo-1526367790999-0150786686a2?w=400&h=400&fit=crop',
    lifestyle: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=480&h=640&fit=crop',
    onion: 'https://images.unsplash.com/photo-1518977822534-7049a61ee0c2?w=300&h=300&fit=crop',
    potato: 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=300&h=300&fit=crop',
    tomato: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=300&h=300&fit=crop',
    pizza: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&h=300&fit=crop',
    cake: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&h=300&fit=crop',
    biryani: 'https://images.unsplash.com/photo-1631515243349-e0cb75fb8d3a?w=300&h=300&fit=crop',
    saree: 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=640&h=400&fit=crop',
    decor: 'https://images.unsplash.com/photo-1514222134-b57cbb8ce073?w=400&h=400&fit=crop',
    shoe: 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=300&h=300&fit=crop',
    gift: 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=400&h=300&fit=crop',
    wine: 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=400&h=400&fit=crop',
    dining: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=400&fit=crop',
    cleaning: 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=400&h=400&fit=crop',
    breakfast: 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=400&h=400&fit=crop',
    baby: 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=400&h=400&fit=crop',
    fitness: 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400&h=400&fit=crop',
    phone: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
    laptop: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop',
    kids: 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=400&h=400&fit=crop',
    prasad: 'https://images.unsplash.com/photo-1606491956689-2ea866880c84?w=400&h=400&fit=crop',
    allfood: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300&h=300&fit=crop'
  };

  var LOGO = 'https://efactor.tech/files/home/Logo_new.png';

  function brandBar(subtitle) {
    var sub = subtitle == null ? 'Smart ecosystem' : String(subtitle);
    return '<div class="ahp-sui-brandbar">' +
      '<img src="' + LOGO + '" alt="eFactor">' +
      '<div><strong>eFactor</strong>' + (sub ? '<small>' + sub + '</small>' : '') + '</div>' +
    '</div>';
  }

  function bg(url, fallback) {
    return 'background-color:' + (fallback || '#e5e7eb') + ';background-image:url(\'' + url + '\');background-size:cover;background-position:center';
  }

  function catCircle(label, img, active) {
    return '<div class="ahp-sui-cat' + (active ? ' is-active' : '') + '">' +
      '<span class="ahp-sui-cat-img" style="' + bg(img) + '"></span>' +
      '<span class="ahp-sui-cat-label">' + label + '</span></div>';
  }

  function dealCard(opts) {
    return '<article class="ahp-sui-deal-card" style="' + bg(opts.img) + '">' +
      '<div class="ahp-sui-deal-shade"></div>' +
      '<div class="ahp-sui-deal-top">' +
        (opts.tag ? '<span class="tag">' + opts.tag + '</span>' : '') +
        (opts.offer ? '<span class="offer">' + opts.offer + '</span>' : '') +
      '</div>' +
      '<div class="ahp-sui-deal-bot"><h4>' + opts.title + '</h4><p>' + (opts.sub || '') + '</p></div>' +
    '</article>';
  }

  function buildStyle() {
    return '<div class="ahp-sui ahp-sui-style">' +
      brandBar('Style · Fashion & lifestyle') +
      '<div class="ahp-sui-toprow">' +
        '<div class="ahp-sui-loc"><i class="fas fa-map-marker-alt"></i><div><strong>Deliver to Efactor Hub</strong><small>Your saved address</small></div><i class="fas fa-chevron-down"></i></div>' +
        '<div class="ahp-sui-wallet"><i class="fas fa-wallet"></i> ₹0</div>' +
      '</div>' +
      '<div class="ahp-sui-searchrow">' +
        '<div class="ahp-sui-search"><img src="' + LOGO + '" alt="eFactor"><span>Search “Pants”</span><i class="fas fa-microphone"></i><i class="fas fa-camera"></i></div>' +
        '<button type="button" class="ahp-sui-iconbtn"><i class="fas fa-bell"></i></button>' +
        '<button type="button" class="ahp-sui-iconbtn"><i class="fas fa-heart"></i></button>' +
        '<button type="button" class="ahp-sui-iconbtn"><i class="fas fa-user"></i></button>' +
      '</div>' +
      '<div class="ahp-sui-tabs"><span class="is-active">ALL</span><span>MEN</span><span>WOMEN</span><span>KIDS</span><i class="fas fa-th"></i></div>' +
      '<div class="ahp-sui-hscroll">' +
        catCircle('Fashion', IMG.fashion, true) +
        catCircle('Beauty', IMG.beauty, false) +
        catCircle('Homeliving', IMG.home, false) +
        catCircle('Footwear', IMG.shoe, false) +
        catCircle('Accessories', IMG.gift, false) +
      '</div>' +
      '<div class="ahp-sui-strip"><span>Free shipping over ₹499</span><span>Efactor Club 10% extra</span><span>New drops daily</span></div>' +
      '<div class="ahp-sui-look-grid">' +
        '<article class="ahp-sui-look"><div class="ahp-sui-look-img" style="' + bg(IMG.fashion) + '"></div><div class="ahp-sui-look-foot"><strong>Efactor Edit</strong><span>Min. 35% off</span></div></article>' +
        '<article class="ahp-sui-look"><div class="ahp-sui-look-img" style="' + bg(IMG.fashion2) + '"></div><div class="ahp-sui-look-foot"><strong>Efactor Studio</strong><span>Min. 40% off</span></div></article>' +
      '</div>' +
      '<div class="ahp-sui-banner-text">LOWEST PRICE IN 30 DAYS</div>' +
      '<div class="ahp-sui-prod-grid">' +
        '<article><div class="ahp-sui-prod-img" style="' + bg(IMG.beauty) + '"></div><strong>Efactor Luxe</strong><small>Accessories · From ₹199</small></article>' +
        '<article><div class="ahp-sui-prod-img" style="' + bg(IMG.lifestyle) + '"></div><strong>Efactor Soft</strong><small>Loungewear · From ₹299</small></article>' +
        '<article><div class="ahp-sui-prod-img" style="' + bg(IMG.shoe) + '"></div><strong>Efactor Steps</strong><small>Footwear · From ₹399</small></article>' +
        '<article><div class="ahp-sui-prod-img" style="' + bg(IMG.fashion3) + '"></div><strong>Efactor Street</strong><small>Casual wear · From ₹249</small></article>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-title">Trending on Efactor</h3>' +
      '<div class="ahp-sui-hscroll ahp-sui-trend">' +
        '<article><div style="' + bg(IMG.saree) + '"></div><strong>Sarees</strong><small>From ₹189</small></article>' +
        '<article><div style="' + bg(IMG.beauty2) + '"></div><strong>Beauty kits</strong><small>From ₹149</small></article>' +
        '<article><div style="' + bg(IMG.home2) + '"></div><strong>Home decor</strong><small>From ₹199</small></article>' +
      '</div>' +
    '</div>';
  }

  function buildGrocery() {
    return '<div class="ahp-sui ahp-sui-grocery">' +
      brandBar('Grocery · Everyday essentials') +
      '<div class="ahp-sui-searchrow">' +
        '<div class="ahp-sui-search wide"><img class="ahp-sui-inline-logo" src="' + LOGO + '" alt="eFactor"><i class="fas fa-search"></i><span>Search “atta & more”</span></div>' +
        '<div class="ahp-sui-storechip"><span class="ahp-sui-storechip-img" style="' + bg(IMG.decor) + '"></span>Efactor Store</div>' +
      '</div>' +
      '<section class="ahp-sui-green-block">' +
        '<div class="ahp-sui-copower">Co-powered by <b>Efactor Brands</b></div>' +
        '<div class="ahp-sui-cat-grid3">' +
          '<article class="filled"><div class="txt"><h4>Cooking Essentials</h4><p>UP TO 70% OFF</p></div><span style="' + bg(IMG.grocery) + '"></span></article>' +
          '<article class="filled"><div class="txt"><h4>Personal Care</h4><p>UP TO 85% OFF</p></div><span style="' + bg(IMG.beauty) + '"></span></article>' +
          '<article class="filled"><div class="txt"><h4>Cleaning Essentials</h4><p>UP TO 80% OFF</p></div><span style="' + bg(IMG.cleaning) + '"></span></article>' +
          '<article class="filled"><div class="txt"><h4>Breakfast & Instant</h4><p>UP TO 60% OFF</p></div><span style="' + bg(IMG.breakfast) + '"></span></article>' +
          '<article class="filled"><div class="txt"><h4>Baby Care</h4><p>UP TO 55% OFF</p></div><span style="' + bg(IMG.baby) + '"></span></article>' +
          '<article class="filled"><div class="txt"><h4>Protein & Fitness</h4><p>UP TO 45% OFF</p></div><span style="' + bg(IMG.fitness) + '"></span></article>' +
        '</div>' +
        '<div class="ahp-sui-gift-banner"><span class="ahp-sui-gift-img" style="' + bg(IMG.gift) + '"></span><div><strong>Gifts of Gratitude</strong><small>Order gifts powered by Efactor</small></div><button type="button"><i class="fas fa-arrow-right"></i></button></div>' +
      '</section>' +
      '<section class="ahp-sui-explore">' +
        '<h3>Explore</h3>' +
        '<div class="ahp-sui-explore-grid">' +
          '<article class="sq pink filled-bg" style="' + bg(IMG.decor) + '"><div class="ov"><strong>Festival</strong><small>Efactor Picks</small></div></article>' +
          '<article class="wide cream filled-bg" style="' + bg(IMG.grocery2) + '"><div class="ov"><strong>select</strong><p>Find products you\'ll love</p><span>Gourmet finds</span></div></article>' +
          '<article class="sq soft filled-bg" style="' + bg(IMG.phone) + '"><div class="ov"><strong>Super Mall</strong><small>Gadgets & more</small></div></article>' +
          '<article class="sq soft filled-bg" style="' + bg(IMG.gift) + '"><div class="ov"><strong>50% Off</strong><small>Daily deals</small></div></article>' +
          '<article class="sq soft filled-bg" style="' + bg(IMG.tomato) + '"><div class="ov"><strong>Fresh</strong><small>Farm picks</small></div></article>' +
        '</div>' +
      '</section>' +
      '<h3 class="ahp-sui-sec-title">Fresh at ₹5</h3>' +
      '<p class="ahp-sui-sub">Pick any 2 items from below</p>' +
      '<div class="ahp-sui-hscroll ahp-sui-buyagain">' +
        '<article><div class="pic" style="' + bg(IMG.onion) + '"></div><strong>₹5</strong> <s>₹63</s><p>Onion · 500 g</p><button type="button">ADD</button></article>' +
        '<article><div class="pic" style="' + bg(IMG.potato) + '"></div><strong>₹5</strong> <s>₹45</s><p>Potato · 1 kg</p><button type="button">ADD</button></article>' +
        '<article><div class="pic" style="' + bg(IMG.tomato) + '"></div><strong>₹5</strong> <s>₹40</s><p>Tomato · 500 g</p><button type="button">ADD</button></article>' +
      '</div>' +
      '<div class="ahp-sui-float-cta"><i class="fas fa-motorcycle"></i><div><strong>Unlock free delivery</strong><small>Shop for ₹149</small></div></div>' +
    '</div>';
  }

  function buildMarket() {
    return '<div class="ahp-sui ahp-sui-market">' +
      brandBar('Market · Shop everything') +
      '<div class="ahp-sui-searchrow">' +
        '<div class="ahp-sui-search wide"><img class="ahp-sui-inline-logo" src="' + LOGO + '" alt="eFactor"><i class="fas fa-search"></i><span>Search or ask a question</span><i class="fas fa-camera"></i><i class="fas fa-microphone"></i></div>' +
        '<button type="button" class="ahp-sui-iconbtn"><i class="fas fa-qrcode"></i></button>' +
      '</div>' +
      '<div class="ahp-sui-icon-grid">' +
        '<div><span class="ic" style="' + bg(IMG.gift) + '"></span><span>Prime</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.lifestyle) + '"></span><span>Shop Live</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.phone) + '"></span><span>Mobiles</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.beauty) + '"></span><span>Rewards</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.gift) + '"></span><span>Deals</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.laptop) + '"></span><span>Electronics</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.home) + '"></span><span>Home</span></div>' +
        '<div><span class="ic" style="' + bg(IMG.grocery) + '"></span><span>Daily Needs</span></div>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-title">eFactor Specials | Shop now</h3>' +
      '<div class="ahp-sui-hscroll">' +
        '<article class="ahp-sui-special"><div style="' + bg(IMG.grocery2) + '"></div><span>Shop All</span></article>' +
        '<article class="ahp-sui-special"><div style="' + bg(IMG.gift) + '"></div><span>Pooja Kits</span></article>' +
        '<article class="ahp-sui-special"><div style="' + bg(IMG.breakfast) + '"></div><span>Fasting Essentials</span></article>' +
        '<article class="ahp-sui-special"><div style="' + bg(IMG.decor) + '"></div><span>Festival Decor</span></article>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-title">Starting ₹199 | Home essentials | eFactor Brands</h3>' +
      '<div class="ahp-sui-prod-grid">' +
        '<article><div class="ahp-sui-prod-img tall" style="' + bg(IMG.home) + '"></div><strong>Starting ₹299 | Bedsheets</strong></article>' +
        '<article><div class="ahp-sui-prod-img tall" style="' + bg(IMG.home2) + '"></div><strong>Starting ₹199 | Home decor</strong></article>' +
        '<article><div class="ahp-sui-prod-img tall" style="' + bg(IMG.cleaning) + '"></div><strong>Starting ₹149 | Cleaning</strong></article>' +
        '<article><div class="ahp-sui-prod-img tall" style="' + bg(IMG.food) + '"></div><strong>Starting ₹249 | Kitchen</strong></article>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-title">Top picks for you</h3>' +
      '<div class="ahp-sui-hscroll ahp-sui-trend">' +
        '<article><div style="' + bg(IMG.phone) + '"></div><strong>Mobiles</strong><small>From ₹6,999</small></article>' +
        '<article><div style="' + bg(IMG.beauty2) + '"></div><strong>Beauty</strong><small>From ₹199</small></article>' +
        '<article><div style="' + bg(IMG.laptop) + '"></div><strong>Laptops</strong><small>From ₹29,999</small></article>' +
      '</div>' +
    '</div>';
  }

  function buildDeals() {
    return '<div class="ahp-sui ahp-sui-deals">' +
      brandBar('Deals · Offers near you') +
      '<div class="ahp-sui-toprow">' +
        '<div class="ahp-sui-loc pink"><i class="fas fa-map-marker-alt"></i><div><strong>Efactor Local</strong><small>Hanuman Nagar…</small></div><i class="fas fa-chevron-down"></i></div>' +
        '<div class="ahp-sui-tools">' +
          '<button type="button" class="ahp-sui-iconbtn"><i class="fas fa-search"></i></button>' +
          '<span class="ahp-sui-points"><i class="fas fa-coins"></i> 858</span>' +
          '<button type="button" class="ahp-sui-iconbtn"><i class="fas fa-bell"></i></button>' +
        '</div>' +
      '</div>' +
      '<article class="ahp-sui-promo">' +
        '<div class="ahp-sui-promo-copy">' +
          '<img src="' + LOGO + '" alt="eFactor">' +
          '<h2>Get Chicken Single Biryani at <em>₹99</em></h2>' +
          '<p class="ahp-sui-promo-note">Limited time · home delivery only</p>' +
          '<button type="button">Order now →</button>' +
        '</div>' +
        '<div class="ahp-sui-promo-img" style="' + bg(IMG.food) + '"></div>' +
        '<span class="ahp-sui-badge">home delivery</span>' +
      '</article>' +
      '<div class="ahp-sui-deal-grid">' +
        dealCard({ title: 'Fashion', sub: 'Trendy looks near you', tag: 'Going Out', img: IMG.fashion3 }) +
        dealCard({ title: 'Food Delivery', sub: 'Meals in 30 mins', offer: 'Get 85% off', img: IMG.delivery }) +
        dealCard({ title: 'Budget Dining', sub: 'Under ₹299 deals', tag: 'Going Out', img: IMG.dining }) +
        dealCard({ title: 'Online Vouchers', sub: 'Efactor Pay rewards', img: IMG.gift }) +
        dealCard({ title: 'Fine Dining', sub: 'Reserve tonight', tag: 'Going Out', img: IMG.wine }) +
        dealCard({ title: 'The ₹101 Shop', sub: 'Everyday value', offer: 'eFactor Shop', img: IMG.grocery2 }) +
      '</div>' +
      '<button type="button" class="ahp-sui-viewmore">View More &gt;</button>' +
      '<h3 class="ahp-sui-sec-title">Save 20% on favorite brands</h3>' +
      '<div class="ahp-sui-hscroll ahp-sui-trend">' +
        '<article><div style="' + bg(IMG.beauty) + '"></div><strong>Beauty</strong><small>20% cashback</small></article>' +
        '<article><div style="' + bg(IMG.phone) + '"></div><strong>Gadgets</strong><small>Flat ₹100 off</small></article>' +
        '<article><div style="' + bg(IMG.saree) + '"></div><strong>Ethnic</strong><small>Buy 1 Get 1</small></article>' +
      '</div>' +
    '</div>';
  }

  function buildFood() {
    return '<div class="ahp-sui ahp-sui-food">' +
      brandBar('Food · Order in minutes') +
      '<div class="ahp-sui-searchrow">' +
        '<div class="ahp-sui-search wide"><img class="ahp-sui-inline-logo" src="' + LOGO + '" alt="eFactor"><i class="fas fa-search"></i><span>Search “Efactor Deals”</span><i class="fas fa-microphone"></i></div>' +
        '<label class="ahp-sui-veg"><span>VEG</span><i></i></label>' +
      '</div>' +
      '<div class="ahp-sui-hscroll ahp-sui-foodcats">' +
        '<article class="ahp-sui-mealcard"><strong>MEALS UNDER ₹250</strong><span>Explore &gt;</span></article>' +
        catCircle('All', IMG.allfood, true) +
        catCircle('Biryani', IMG.biryani, false) +
        catCircle('Cake', IMG.cake, false) +
        catCircle('Pizza', IMG.pizza, false) +
      '</div>' +
      '<div class="ahp-sui-chips"><span><i class="fas fa-sliders-h"></i> Filters</span><span class="hot"><i class="fas fa-bolt"></i> Near & Fast</span><span>Great Offers</span></div>' +
      '<h3 class="ahp-sui-sec-caps">RECOMMENDED FOR YOU</h3>' +
      '<div class="ahp-sui-food-grid">' +
        '<article><div class="pic" style="' + bg(IMG.food2) + '"><span class="off">₹100 OFF on Efactor</span><span class="rate">★ 4.2</span></div><strong>Efactor Kitchen</strong><small><i class="far fa-clock"></i> 25-30 mins</small><em><i class="fas fa-bolt"></i> Fast Delivery</em></article>' +
        '<article><div class="pic" style="' + bg(IMG.pizza) + '"><span class="off">40% OFF up to ₹120</span><span class="rate">★ 4.4</span></div><strong>Efactor Cafe</strong><small><i class="far fa-clock"></i> 20-25 mins</small><em><i class="fas fa-bolt"></i> Near & Fast</em></article>' +
        '<article><div class="pic" style="' + bg(IMG.biryani) + '"><span class="off">₹80 OFF above ₹249</span><span class="rate">★ 4.5</span></div><strong>Efactor Biryani House</strong><small><i class="far fa-clock"></i> 30-35 mins</small><em><i class="fas fa-bolt"></i> Fast Delivery</em></article>' +
        '<article><div class="pic" style="' + bg(IMG.dining) + '"><span class="off">Flat 30% OFF</span><span class="rate">★ 4.3</span></div><strong>Efactor Gourmet</strong><small><i class="far fa-clock"></i> 35-40 mins</small><em><i class="fas fa-bolt"></i> Near & Fast</em></article>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-caps">EXPLORE MORE</h3>' +
      '<div class="ahp-sui-hscroll">' +
        '<article class="ahp-sui-explore-tile"><i class="fas fa-tags"></i><span>Efactor Offers</span></article>' +
        '<article class="ahp-sui-explore-tile"><i class="fas fa-calendar-check"></i><span>Subscriptions</span></article>' +
        '<article class="ahp-sui-explore-tile"><i class="fas fa-truck"></i><span>Track Order</span></article>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-caps">TRENDING NEAR YOU</h3>' +
      '<div class="ahp-sui-hscroll ahp-sui-trend">' +
        '<article><div style="' + bg(IMG.cake) + '"></div><strong>Efactor Sweets</strong><small>20-25 mins</small></article>' +
        '<article><div style="' + bg(IMG.allfood) + '"></div><strong>Efactor Thali</strong><small>15-20 mins</small></article>' +
        '<article><div style="' + bg(IMG.food) + '"></div><strong>Efactor Grill</strong><small>25-30 mins</small></article>' +
      '</div>' +
    '</div>';
  }

  function buildQuick() {
    return '<div class="ahp-sui ahp-sui-quick">' +
      brandBar('') +
      '<div class="ahp-sui-toprow">' +
        '<div></div>' +
        '<div class="ahp-sui-tools"><span class="ahp-sui-wallet round"><i class="fas fa-wallet"></i> ₹0</span><button type="button" class="ahp-sui-iconbtn round"><i class="fas fa-user"></i></button></div>' +
      '</div>' +
      '<div class="ahp-sui-searchrow">' +
        '<div class="ahp-sui-search wide"><img class="ahp-sui-inline-logo" src="' + LOGO + '" alt="eFactor"><i class="fas fa-search"></i><span>Search “janmashtami”</span><i class="fas fa-microphone"></i></div>' +
      '</div>' +
      '<div class="ahp-sui-hscroll ahp-sui-linecats">' +
        '<div class="is-active"><i class="fas fa-border-all"></i><span>All</span></div>' +
        '<div><i class="fas fa-gift"></i><span>Festival</span></div>' +
        '<div><i class="fas fa-child"></i><span>Kids</span></div>' +
        '<div><i class="fas fa-headphones"></i><span>Electronics</span></div>' +
        '<div><i class="fas fa-spa"></i><span>Beauty</span></div>' +
      '</div>' +
      '<section class="ahp-sui-festive">' +
        '<div class="ahp-sui-copower">POWERED BY <b>Efactor</b></div>' +
        '<div class="ahp-sui-festive-grid">' +
          '<article class="tall filled-card" style="' + bg(IMG.decor) + '"><div class="ov"><h4>Poshak, Idol & More</h4><div class="price"><s>₹399</s><b>₹159</b></div><p>Efactor Festive Dress</p></div></article>' +
          '<article class="filled-card" style="' + bg(IMG.prasad) + '"><div class="ov"><h4>Prasad Corner</h4><p>Ready packs</p></div></article>' +
          '<article class="filled-card" style="' + bg(IMG.kids) + '"><div class="ov"><h4>Kids Costume</h4><p>Festive wear</p></div></article>' +
          '<article class="filled-card" style="' + bg(IMG.breakfast) + '"><div class="ov"><h4>Fasting Essentials</h4><p>Stock up</p></div></article>' +
          '<article class="filled-card" style="' + bg(IMG.gift) + '"><div class="ov"><h4>Temple Decor</h4><p>Lights & more</p></div></article>' +
        '</div>' +
      '</section>' +
      '<h3 class="ahp-sui-sec-title">Previously bought</h3>' +
      '<div class="ahp-sui-hscroll ahp-sui-buyagain">' +
        '<article><div class="pic" style="' + bg(IMG.beauty) + '"></div><strong>₹179</strong> <s>₹225</s><small>20% OFF</small><p>Efactor Care 120 ml</p><button type="button">ADD</button></article>' +
        '<article><div class="pic" style="' + bg(IMG.grocery) + '"></div><strong>₹99</strong> <s>₹140</s><small>29% OFF</small><p>Efactor Daily Pack</p><button type="button">ADD</button></article>' +
        '<article><div class="pic" style="' + bg(IMG.onion) + '"></div><strong>₹5</strong> <s>₹63</s><small>Fresh</small><p>Onion 500 g</p><button type="button">ADD</button></article>' +
        '<article><div class="pic" style="' + bg(IMG.tomato) + '"></div><strong>₹12</strong> <s>₹35</s><small>Fresh</small><p>Tomato 500 g</p><button type="button">ADD</button></article>' +
      '</div>' +
      '<h3 class="ahp-sui-sec-title">More for your home</h3>' +
      '<div class="ahp-sui-prod-grid">' +
        '<article><div class="ahp-sui-prod-img" style="' + bg(IMG.cleaning) + '"></div><strong>Cleaning</strong><small>UP TO 80% OFF</small></article>' +
        '<article><div class="ahp-sui-prod-img" style="' + bg(IMG.beauty2) + '"></div><strong>Personal Care</strong><small>UP TO 85% OFF</small></article>' +
      '</div>' +
    '</div>';
  }

  var STATIC_UI_TEMPLATES = {
    'ui-style': {
      id: 'ui-style',
      label: 'Efactor Style',
      category: 'Fashion',
      note: 'Fashion lookbook · search · category rail',
      swatches: ['#ffffff', '#fdf2f8', '#db2777', '#111827'],
      previewImages: [IMG.fashion, IMG.fashion2, IMG.beauty, IMG.shoe],
      phoneBg: '#ffffff',
      navBg: '#ffffff',
      navActive: '#db2777',
      bottomNav: canonicalBottomNav(),
      render: buildStyle
    },
    'ui-grocery': {
      id: 'ui-grocery',
      label: 'Efactor Grocery',
      category: 'Grocery',
      note: 'Green offers grid · Explore · free delivery CTA',
      swatches: ['#d8f5df', '#ffffff', '#16a34a', '#111827'],
      previewImages: [IMG.grocery, IMG.breakfast, IMG.tomato, IMG.gift],
      phoneBg: '#f3f4f6',
      navBg: '#ffffff',
      navActive: '#e11d48',
      bottomNav: canonicalBottomNav(),
      render: buildGrocery
    },
    'ui-market': {
      id: 'ui-market',
      label: 'Efactor Market',
      category: 'Marketplace',
      note: 'Icon grid · specials rail · 2-col essentials',
      swatches: ['#ffffff', '#f3f4f6', '#111827', '#16a34a'],
      previewImages: [IMG.home, IMG.phone, IMG.gift, IMG.laptop],
      phoneBg: '#ffffff',
      navBg: '#ffffff',
      navActive: '#111827',
      bottomNav: canonicalBottomNav(),
      render: buildMarket
    },
    'ui-deals': {
      id: 'ui-deals',
      label: 'Efactor Deals',
      category: 'Deals',
      note: 'Promo hero · 2-col lifestyle cards · View More',
      swatches: ['#f7f5ff', '#ffffff', '#6d28d9', '#ef4444'],
      previewImages: [IMG.food, IMG.fashion3, IMG.delivery, IMG.dining],
      phoneBg: '#f7f5ff',
      navBg: '#ffffff',
      navActive: '#6d28d9',
      bottomNav: canonicalBottomNav(),
      render: buildDeals
    },
    'ui-food': {
      id: 'ui-food',
      label: 'Efactor Food',
      category: 'Food',
      note: 'Search · categories · recommended restaurants',
      swatches: ['#ffffff', '#fee2e2', '#ef4444', '#16a34a'],
      previewImages: [IMG.food2, IMG.pizza, IMG.biryani, IMG.cake],
      phoneBg: '#ffffff',
      navBg: '#ffffff',
      navActive: '#ef4444',
      bottomNav: canonicalBottomNav(),
      render: buildFood
    },
    'ui-quick': {
      id: 'ui-quick',
      label: 'Efactor QuickMart',
      category: 'Quick commerce',
      note: '8-min delivery · festive grid · buy again',
      swatches: ['#ecfeff', '#ffffff', '#0891b2', '#22c55e'],
      previewImages: [IMG.decor, IMG.grocery, IMG.onion, IMG.kids],
      phoneBg: '#ffffff',
      navBg: '#ffffff',
      navActive: '#0891b2',
      bottomNav: canonicalBottomNav(),
      render: buildQuick
    }
  };

  function getTemplate(id) {
    return STATIC_UI_TEMPLATES[id] || null;
  }

  function listTemplates() {
    return Object.keys(STATIC_UI_TEMPLATES).map(function (key) {
      return STATIC_UI_TEMPLATES[key];
    });
  }

  function sectionsFor(templateId) {
    var logo = LOGO;
    switch (templateId) {
      case 'ui-style':
        return [
          { type: 'brandBar', title: 'eFactor', subtitle: 'Style · Fashion & lifestyle', logoUrl: logo },
          { type: 'locationRow', title: 'Deliver to Efactor Hub', subtitle: 'Your saved address', wallet: '₹0' },
          { type: 'search', placeholder: 'Search “Pants”', showLogo: true, actions: ['microphone', 'camera', 'bell', 'heart', 'user'] },
          { type: 'tabs', items: ['ALL', 'MEN', 'WOMEN', 'KIDS'], active: 'ALL' },
          { type: 'categoryRail', items: [
            { label: 'Fashion', image: IMG.fashion, active: true },
            { label: 'Beauty', image: IMG.beauty },
            { label: 'Homeliving', image: IMG.home },
            { label: 'Footwear', image: IMG.shoe },
            { label: 'Accessories', image: IMG.gift }
          ]},
          { type: 'promoStrip', items: ['Free shipping over ₹499', 'Efactor Club 10% extra', 'New drops daily'] },
          { type: 'lookbookGrid', items: [
            { title: 'Efactor Edit', subtitle: 'Min. 35% off', image: IMG.fashion },
            { title: 'Efactor Studio', subtitle: 'Min. 40% off', image: IMG.fashion2 }
          ]},
          { type: 'bannerText', text: 'LOWEST PRICE IN 30 DAYS' },
          { type: 'productGrid', items: [
            { title: 'Efactor Luxe', subtitle: 'Accessories · From ₹199', image: IMG.beauty },
            { title: 'Efactor Soft', subtitle: 'Loungewear · From ₹299', image: IMG.lifestyle },
            { title: 'Efactor Steps', subtitle: 'Footwear · From ₹399', image: IMG.shoe },
            { title: 'Efactor Street', subtitle: 'Casual wear · From ₹249', image: IMG.fashion3 }
          ]},
          { type: 'trendingRail', title: 'Trending on Efactor', items: [
            { title: 'Sarees', subtitle: 'From ₹189', image: IMG.saree },
            { title: 'Beauty kits', subtitle: 'From ₹149', image: IMG.beauty2 },
            { title: 'Home decor', subtitle: 'From ₹199', image: IMG.home2 }
          ]}
        ];
      case 'ui-grocery':
        return [
          { type: 'brandBar', title: 'eFactor', subtitle: 'Grocery · Everyday essentials', logoUrl: logo },
          { type: 'search', placeholder: 'Search atta & more', showLogo: true, trailingLabel: 'Efactor Store', trailingAction: { actionId: 'catalog.openStore', label: 'Efactor Store' }, action: { actionId: 'search.open' } },
          { type: 'offerGrid', title: 'Co-powered by Efactor Brands', items: [
            { title: 'Cooking Essentials', offer: 'UP TO 70% OFF', image: IMG.grocery },
            { title: 'Personal Care', offer: 'UP TO 85% OFF', image: IMG.beauty },
            { title: 'Cleaning Essentials', offer: 'UP TO 80% OFF', image: IMG.cleaning },
            { title: 'Breakfast & Instant', offer: 'UP TO 60% OFF', image: IMG.breakfast },
            { title: 'Baby Care', offer: 'UP TO 55% OFF', image: IMG.baby },
            { title: 'Protein & Fitness', offer: 'UP TO 45% OFF', image: IMG.fitness }
          ]},
          { type: 'promoHero', id: 'gifts-of-gratitude', title: 'Gifts of Gratitude', subtitle: 'Thoughtful picks for every occasion', image: IMG.gift, cta: 'Shop gifts', logoUrl: logo, action: { actionId: 'catalog.openCollection', payload: { collection: 'gifts-of-gratitude' } } },
          { type: 'exploreGrid', title: 'Explore', items: [
            { title: 'Festival', subtitle: 'Efactor Picks', image: IMG.decor, action: { actionId: 'catalog.openCollection', payload: { collection: 'festival' } } },
            { title: 'select', subtitle: 'Gourmet finds', image: IMG.grocery2, wide: true, action: { actionId: 'catalog.openCollection', payload: { collection: 'select' } } },
            { title: 'Super Mall', subtitle: 'Gadgets & more', image: IMG.phone, action: { actionId: 'catalog.openCollection', payload: { collection: 'super-mall' } } },
            { title: '50% Off', subtitle: 'Daily deals', image: IMG.gift, action: { actionId: 'catalog.openCollection', payload: { collection: 'daily-deals' } } },
            { title: 'Fresh', subtitle: 'Farm picks', image: IMG.tomato, action: { actionId: 'catalog.openCollection', payload: { collection: 'fresh' } } }
          ]},
          { type: 'buyAgainRail', title: 'Fresh at ₹5', itemAction: { label: 'ADD', actionId: 'cart.addItem', enabled: true, quantityStep: 1, quantityBinding: 'state.cart.quantities', cartBinding: 'state.cart.items' }, items: [
            { title: 'Onion · 500 g', price: '₹5', image: IMG.onion, sku: 'fresh-onion-500g', quantity: 0, addLabel: 'ADD', enabled: true, action: { actionId: 'cart.addItem', payload: { sku: 'fresh-onion-500g' } } },
            { title: 'Potato · 1 kg', price: '₹5', image: IMG.potato, sku: 'fresh-potato-1kg', quantity: 0, addLabel: 'ADD', enabled: true, action: { actionId: 'cart.addItem', payload: { sku: 'fresh-potato-1kg' } } },
            { title: 'Tomato · 500 g', price: '₹5', image: IMG.tomato, sku: 'fresh-tomato-500g', quantity: 0, addLabel: 'ADD', enabled: true, action: { actionId: 'cart.addItem', payload: { sku: 'fresh-tomato-500g' } } }
          ]},
          { type: 'cta', title: 'Unlock free delivery', subtitle: 'Shop for ₹149', action: { actionId: 'catalog.openCollection', payload: { collection: 'free-delivery' } } }
        ];
      case 'ui-market':
        return [
          { type: 'brandBar', title: 'eFactor', subtitle: 'Market · Shop everything', logoUrl: logo },
          { type: 'search', placeholder: 'Search or ask a question', showLogo: true },
          { type: 'iconGrid', items: [
            { label: 'Prime', image: IMG.gift },
            { label: 'Shop Live', image: IMG.lifestyle },
            { label: 'Mobiles', image: IMG.phone },
            { label: 'Rewards', image: IMG.beauty },
            { label: 'Deals', image: IMG.gift },
            { label: 'Electronics', image: IMG.laptop },
            { label: 'Home', image: IMG.home },
            { label: 'Daily Needs', image: IMG.grocery }
          ]},
          { type: 'specialsRail', title: 'eFactor Specials | Shop now', items: [
            { title: 'Shop All', image: IMG.grocery2 },
            { title: 'Pooja Kits', image: IMG.gift },
            { title: 'Fasting Essentials', image: IMG.breakfast },
            { title: 'Festival Decor', image: IMG.decor }
          ]},
          { type: 'productGrid', title: 'Starting ₹199 | Home essentials', items: [
            { title: 'Starting ₹299 | Bedsheets', image: IMG.home },
            { title: 'Starting ₹199 | Home decor', image: IMG.home2 },
            { title: 'Starting ₹149 | Cleaning', image: IMG.cleaning },
            { title: 'Starting ₹249 | Kitchen', image: IMG.food }
          ]}
        ];
      case 'ui-deals':
        return [
          { type: 'brandBar', title: 'eFactor', subtitle: 'Deals · Offers near you', logoUrl: logo },
          { type: 'promoHero', title: 'Get Chicken Single Biryani at ₹99', subtitle: 'Limited time · home delivery only', image: IMG.food, cta: 'Order now', logoUrl: logo },
          { type: 'dealGrid', items: [
            { title: 'Fashion', subtitle: 'Going out looks', image: IMG.fashion3, offer: 'Min 40% off', tag: 'HOT' },
            { title: 'Food Delivery', subtitle: 'Get 85% off', image: IMG.delivery, offer: 'Up to 85% OFF' },
            { title: 'Budget Dining', subtitle: 'Near you', image: IMG.dining, offer: 'Flat ₹100 off' },
            { title: 'Online Vouchers', subtitle: 'Efactor Pay', image: IMG.gift, offer: 'Extra 10%' }
          ]},
          { type: 'viewMore', label: 'View More Deals' }
        ];
      case 'ui-food':
        return [
          { type: 'brandBar', title: 'eFactor', subtitle: 'Food · Order in minutes', logoUrl: logo },
          { type: 'search', placeholder: 'Search Efactor Deals', showLogo: true, vegToggle: true },
          { type: 'categoryRail', items: [
            { label: 'All', image: IMG.allfood, active: true },
            { label: 'Biryani', image: IMG.biryani },
            { label: 'Cake', image: IMG.cake },
            { label: 'Pizza', image: IMG.pizza }
          ]},
          { type: 'filterChips', items: [
            { label: 'Filters', icon: 'fa-sliders-h' },
            { label: 'Near & Fast', icon: 'fa-bolt', accent: true },
            { label: 'Great Offers' }
          ]},
          { type: 'restaurantGrid', title: 'RECOMMENDED FOR YOU', items: [
            { title: 'Efactor Kitchen', subtitle: '25-30 mins', offer: '₹100 OFF on Efactor', rating: '4.2', badge: 'Fast Delivery', image: IMG.food2 },
            { title: 'Efactor Cafe', subtitle: '20-25 mins', offer: '40% OFF up to ₹120', rating: '4.4', badge: 'Near & Fast', image: IMG.pizza },
            { title: 'Efactor Biryani House', subtitle: '30-35 mins', offer: '₹80 OFF above ₹249', rating: '4.5', badge: 'Fast Delivery', image: IMG.biryani },
            { title: 'Efactor Gourmet', subtitle: '35-40 mins', offer: 'Flat 30% OFF', rating: '4.3', badge: 'Near & Fast', image: IMG.dining }
          ]},
          { type: 'exploreTiles', title: 'EXPLORE MORE', items: [
            { title: 'Efactor Offers', icon: 'fa-tags' },
            { title: 'Subscriptions', icon: 'fa-calendar-check' },
            { title: 'Track Order', icon: 'fa-truck' }
          ]},
          { type: 'trendingRail', title: 'TRENDING NEAR YOU', items: [
            { title: 'Efactor Sweets', subtitle: '20-25 mins', image: IMG.cake },
            { title: 'Efactor Thali', subtitle: '15-20 mins', image: IMG.allfood },
            { title: 'Efactor Grill', subtitle: '25-30 mins', image: IMG.food }
          ]}
        ];
      case 'ui-quick':
        return [
          { type: 'brandBar', title: 'eFactor', subtitle: '', logoUrl: logo },
          { type: 'search', placeholder: 'Search janmashtami', showLogo: true },
          { type: 'lineCategories', items: ['All', 'Festival', 'Kids', 'Electronics', 'Beauty'] },
          { type: 'festiveGrid', poweredBy: 'Efactor', items: [
            { title: 'Poshak, Idol & More', price: '₹159', was: '₹399', image: IMG.decor, tall: true },
            { title: 'Prasad Corner', subtitle: 'Ready packs', image: IMG.prasad },
            { title: 'Kids Costume', subtitle: 'Festive wear', image: IMG.kids },
            { title: 'Fasting Essentials', subtitle: 'Stock up', image: IMG.breakfast },
            { title: 'Temple Decor', subtitle: 'Lights & more', image: IMG.gift }
          ]},
          { type: 'buyAgainRail', title: 'Previously bought', items: [
            { title: 'Efactor Care 120 ml', price: '₹179', image: IMG.beauty },
            { title: 'Efactor Daily Pack', price: '₹99', image: IMG.grocery },
            { title: 'Onion 500 g', price: '₹5', image: IMG.onion },
            { title: 'Tomato 500 g', price: '₹12', image: IMG.tomato }
          ]}
        ];
      default:
        return [];
    }
  }

  /**
   * Every section item must be an object so a strict renderer can parse one shape.
   * Shorthand string items (tabs, promoStrip, lineCategories) become { label }.
   */
  function normalizeSections(sections) {
    return (sections || []).map(function (section) {
      if (!section || !Array.isArray(section.items)) return section;
      var items = section.items.map(function (item) {
        return (typeof item === 'string' || typeof item === 'number')
          ? { label: String(item) }
          : item;
      });
      return Object.assign({}, section, { items: items });
    });
  }

  /**
   * Canonical component contract (schema.componentDefinitions) that renders each
   * section type. Templates must never invent a parameter contract for behaviour
   * the default Home schema already supports, so every section declares the
   * existing component that draws it. Types absent from this map have no canonical
   * equivalent and are reported as new renderer requirements instead.
   */
  var SECTION_CANONICAL_COMPONENT = {
    brandBar: 'pageHeader',
    bannerText: 'banner',
    promoStrip: 'banner',
    promoHero: 'banner',
    cta: 'banner',
    locationRow: 'banner',
    etaHeader: 'banner',
    viewMore: 'actionButton',
    categoryRail: 'list',
    trendingRail: 'list',
    specialsRail: 'list',
    buyAgainRail: 'list',
    lineCategories: 'list',
    offerGrid: 'responsiveGrid',
    exploreGrid: 'responsiveGrid',
    exploreTiles: 'responsiveGrid',
    dealGrid: 'responsiveGrid',
    productGrid: 'responsiveGrid',
    festiveGrid: 'responsiveGrid',
    lookbookGrid: 'responsiveGrid',
    restaurantGrid: 'responsiveGrid',
    iconGrid: 'responsiveGrid'
  };

  function annotateCanonicalComponents(sections) {
    var mapped = [];
    var missing = [];
    var annotated = (sections || []).map(function (section) {
      if (!section || !section.type) return section;
      var canonical = SECTION_CANONICAL_COMPONENT[section.type] || null;
      if (canonical) {
        if (mapped.indexOf(section.type) === -1) mapped.push(section.type);
      } else if (missing.indexOf(section.type) === -1) {
        missing.push(section.type);
      }
      return Object.assign({}, section, { canonicalComponent: canonical });
    });

    return { sections: annotated, mapped: mapped, requiresNewComponent: missing };
  }

  /**
   * Swatch order is consistent across every kit:
   * [0] section tint, [1] alternate surface, [2] content accent, [3] secondary accent.
   */
  function semanticColors(tpl) {
    var swatches = tpl.swatches || [];
    return {
      pageBackground: tpl.phoneBg,
      surface: '#ffffff',
      surfaceAlt: swatches[1] || '#ffffff',
      sectionBackground: swatches[0] || tpl.phoneBg,
      contentAccent: swatches[2] || tpl.navActive,
      secondaryAccent: swatches[3] || tpl.navActive,
      offer: swatches[2] || tpl.navActive,
      navBackground: tpl.navBg,
      navActive: tpl.navActive,
      navInactive: '#6b7280',
      textPrimary: '#111827',
      textSecondary: '#374151',
      textMuted: '#6b7280'
    };
  }

  function toSchema(templateId, cfg) {
    cfg = cfg || {};
    var tpl = getTemplate(templateId);
    if (!tpl) return null;
    var canonical = annotateCanonicalComponents(normalizeSections(sectionsFor(tpl.id)));
    return {
      enabled: true,
      mode: 'staticHomeUi',
      templateId: tpl.id,
      label: tpl.label,
      category: tpl.category || tpl.label,
      brand: {
        name: 'eFactor',
        logoUrl: cfg.logoUrl || LOGO
      },
      appearance: {
        phoneBg: tpl.phoneBg,
        navBg: tpl.navBg,
        navActiveColor: tpl.navActive,
        surfaceColor: '#ffffff',
        textPrimary: '#111827',
        textSecondary: '#374151',
        textMuted: '#6b7280',
        accentColor: tpl.navActive,
        bgStyle: 'light',
        // Named roles so a renderer never has to guess what swatches[n] means.
        // contentAccent is deliberately separate from navActive: grocery uses a
        // green accent in content while the active nav item is rose.
        colors: semanticColors(tpl),
        swatches: (tpl.swatches || []).slice()
      },
      previewImages: (tpl.previewImages || []).slice(),
      bottomNav: JSON.parse(JSON.stringify(tpl.bottomNav || [])),
      sections: canonical.sections,
      rendererKey: 'staticHomeUi',
      contractVersion: '1.1',
      // Which canonical components render this template, and what genuinely has no
      // existing equivalent. Lets a renderer reuse components it already supports.
      rendererRequirements: {
        canonicalComponents: canonical.mapped.map(function (type) {
          return { sectionType: type, component: SECTION_CANONICAL_COMPONENT[type] };
        }),
        requiresNewComponent: canonical.requiresNewComponent.slice()
      },
      replacesCubeHome: true,
      note: 'Static home UI kit. Flutter must render ONLY components.staticHomeUi.sections (+ bottomNav). Ignore pageHeader/welcome/grid when uiMode=staticHomeUi.'
    };
  }

  function renderTo(root, templateId) {
    if (!root) return false;
    var tpl = getTemplate(templateId);
    if (!tpl) {
      root.innerHTML = '';
      root.classList.add('ahp-is-hidden');
      return false;
    }
    root.innerHTML = tpl.render();
    root.classList.remove('ahp-is-hidden');
    root.setAttribute('data-static-ui', tpl.id);
    return true;
  }

  global.AhpStaticUi = {
    templates: STATIC_UI_TEMPLATES,
    logoUrl: LOGO,
    list: listTemplates,
    get: getTemplate,
    toSchema: toSchema,
    renderTo: renderTo
  };
})(typeof window !== 'undefined' ? window : globalThis);
