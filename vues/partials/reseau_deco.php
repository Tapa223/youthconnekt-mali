<?php /* Décor : fils et points de connexion de la charte. Les fils se tracent quand le bloc apparaît. Paramètre : $opacite */ ?>
<svg class="reseau pointer-events-none absolute inset-0 h-full w-full <?= e($opacite ?? 'opacity-60') ?>" viewBox="0 0 1440 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true" data-reseau>
  <g fill="none" stroke="rgb(255 255 255 / .16)" stroke-width="1.2">
    <path pathLength="1" d="M0 80 L230 20 L470 135 L720 0"/><path pathLength="1" d="M230 20 L180 410 L520 600"/><path pathLength="1" d="M720 0 L950 70 L1440 -10"/>
    <path pathLength="1" d="M950 70 L1200 330 L1440 250"/><path pathLength="1" d="M1200 330 L1050 600"/><path pathLength="1" d="M0 480 L180 410"/>
  </g>
  <circle cx="230" cy="20" r="7" fill="#F1592A"/><circle cx="470" cy="135" r="5" fill="#04B2B1"/>
  <circle cx="950" cy="70" r="6" fill="#04B2B1"/><circle cx="1200" cy="330" r="7" fill="#F1592A"/>
  <circle cx="180" cy="410" r="5" fill="#04B2B1"/>
</svg>
