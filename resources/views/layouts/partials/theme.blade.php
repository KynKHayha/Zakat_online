{{-- Design system global: font, token warna, bayangan, animasi. Dipakai semua layout. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    arabic: ['Amiri', 'serif'],
                },
                colors: {
                    brand: {
                        50:  '#ecfdf5',
                        100: '#d1fae5',
                        200: '#a7f3d0',
                        300: '#6ee7b7',
                        400: '#34d399',
                        500: '#10b981',
                        600: '#059669',
                        700: '#047857',
                        800: '#065f46',
                        900: '#064e3b',
                    }
                },
                boxShadow: {
                    soft: '0 4px 24px -6px rgba(5, 150, 105, 0.12)',
                    card: '0 2px 12px -4px rgba(15, 23, 42, 0.08)',
                    glow: '0 8px 32px -8px rgba(5, 150, 105, 0.35)',
                },
                borderRadius: { '4xl': '2rem' },
            }
        }
    }
</script>
<style>
    body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
    ::selection { background: #a7f3d0; color: #064e3b; }

    .num { font-variant-numeric: tabular-nums; letter-spacing: -0.01em; }

    /* Pola titik halus di atas elemen ber-gradient.
       Pakai pseudo-element (bukan background-image) agar tidak menimpa
       background-image dari kelas Tailwind bg-gradient-*. */
    .hero-pattern::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image:
            radial-gradient(rgba(255,255,255,0.14) 1px, transparent 1.4px),
            radial-gradient(rgba(255,255,255,0.07) 1px, transparent 1.4px);
        background-size: 24px 24px, 24px 24px;
        background-position: 0 0, 12px 12px;
    }

    .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    .card-hover { transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(5,150,105,.25); }

    .float-slow { animation: floatY 6s ease-in-out infinite; }
    .float-slower { animation: floatY 8s ease-in-out infinite reverse; }
    @keyframes floatY { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }

    .fade-up { animation: fadeUp .45s ease both; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

    /* Input dasar: tinggi nyaman untuk jari (min 48px) dan focus ring hijau lembut */
    .fld {
        width: 100%; border: 2px solid #e5e7eb; background: #fff; border-radius: 1rem;
        padding: .9rem 1.1rem; font-size: .95rem; font-weight: 500; color: #1f2937;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .fld:hover:not(:focus) { border-color: #a7f3d0; }
    .fld:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.12); background: #fff; }
    .fld-lg { font-size: 1.25rem; font-weight: 700; padding: 1.1rem 1.25rem; }
    .fld-error { border-color: #fca5a5; }
    .fld-error:focus { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239,68,68,.12); }
    .fld-label { display: block; font-size: .82rem; font-weight: 700; color: #374151; margin-bottom: .5rem; }

    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .55rem;
        font-weight: 800; border-radius: 1rem; padding: .9rem 1.6rem; font-size: .9rem;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, color .18s ease, border-color .18s ease;
        cursor: pointer; white-space: nowrap;
    }
    .btn:active { transform: translateY(1px) scale(.99); }
    .btn-primary { background: linear-gradient(135deg, #059669, #047857); color: #fff; box-shadow: 0 8px 24px -8px rgba(5,150,105,.5); }
    .btn-primary:hover { box-shadow: 0 12px 30px -8px rgba(5,150,105,.65); transform: translateY(-2px); }
    .btn-ghost { background: #f3f4f6; color: #374151; }
    .btn-ghost:hover { background: #e5e7eb; }
    .btn-outline { background: #fff; color: #047857; border: 2px solid #a7f3d0; }
    .btn-outline:hover { background: #ecfdf5; border-color: #10b981; }
    .btn:disabled { background: #f3f4f6; color: #9ca3af; box-shadow: none; transform: none; cursor: not-allowed; }

    .chip {
        display: inline-flex; align-items: center; gap: .4rem; font-size: .72rem; font-weight: 800;
        padding: .35rem .8rem; border-radius: 999px; letter-spacing: .02em;
    }

    .status-badge {
        display: inline-flex; align-items: center; gap: .4rem; font-size: .72rem; font-weight: 800;
        padding: .35rem .7rem; border-radius: 999px; white-space: nowrap;
    }
</style>
