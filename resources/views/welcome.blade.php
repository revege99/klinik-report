<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Klink Report</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f4efe6;
                --panel: #fffdf8;
                --text: #1f2937;
                --muted: #6b7280;
                --accent: #0f766e;
                --accent-soft: #ccfbf1;
                --border: #e7dcc9;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: Georgia, "Times New Roman", serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top left, rgba(15, 118, 110, 0.12), transparent 30%),
                    radial-gradient(circle at bottom right, rgba(217, 119, 6, 0.12), transparent 28%),
                    var(--bg);
            }

            .shell {
                width: min(980px, calc(100% - 32px));
                margin: 40px auto;
            }

            .hero {
                display: grid;
                gap: 24px;
                padding: 32px;
                border: 1px solid var(--border);
                border-radius: 28px;
                background: rgba(255, 253, 248, 0.92);
                box-shadow: 0 24px 70px rgba(31, 41, 55, 0.08);
            }

            .eyebrow {
                display: inline-block;
                padding: 8px 14px;
                border-radius: 999px;
                background: var(--accent-soft);
                color: var(--accent);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.18em;
                text-transform: uppercase;
            }

            h1 {
                margin: 16px 0 12px;
                font-size: clamp(2rem, 5vw, 3.5rem);
                line-height: 1.05;
            }

            p {
                margin: 0;
                color: var(--muted);
                font-size: 1.02rem;
                line-height: 1.8;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
                margin-top: 28px;
            }

            .card {
                padding: 20px;
                border: 1px solid var(--border);
                border-radius: 22px;
                background: #fff;
            }

            .card h2 {
                margin: 0 0 12px;
                font-size: 1rem;
            }

            .card code {
                display: inline-block;
                margin-top: 10px;
                padding: 6px 10px;
                border-radius: 12px;
                background: #f3f4f6;
                color: #111827;
                font-size: 0.92rem;
            }

            .steps {
                margin-top: 28px;
                padding: 24px;
                border-radius: 24px;
                background: #fff;
                border: 1px solid var(--border);
            }

            .steps h2 {
                margin: 0 0 12px;
                font-size: 1.2rem;
            }

            ol {
                margin: 0;
                padding-left: 20px;
                color: var(--muted);
                line-height: 1.9;
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="hero">
                <div>
                    <span class="eyebrow">Single Clinic Setup</span>
                    <h1>Klink Report siap dipakai untuk satu klinik lokal.</h1>
                    <p>
                        Aplikasi ini membaca data dari database SIMRS klinik lokal, tetapi menyimpan input final
                        dan laporan pendapatan ke database aplikasi yang terpisah. Struktur dibuat sederhana agar
                        mudah diuji dulu di satu klinik, lalu bisa disalin ke klinik lain.
                    </p>
                </div>

                <div class="grid">
                    <article class="card">
                        <h2>Database aplikasi</h2>
                        <p>Menyimpan data input final, master layanan, pengeluaran, dan laporan.</p>
                        <code>klink_report</code>
                    </article>

                    <article class="card">
                        <h2>Koneksi baca SIMRS</h2>
                        <p>Disiapkan terpisah lewat koneksi Laravel bernama <strong>simrs</strong>.</p>
                        <code>SIMRS_DB_DATABASE=simrs</code>
                    </article>

                    <article class="card">
                        <h2>Struktur inti</h2>
                        <p>Tabel utama: <strong>rekap_pasien</strong>, <strong>pengeluaran</strong>, dan master.</p>
                        <code>master_layanan</code>
                    </article>
                </div>

                <section class="steps">
                    <h2>Langkah berikutnya</h2>
                    <ol>
                        <li>Sesuaikan nama database SIMRS asli di file <code>.env</code>.</li>
                        <li>Tentukan tabel dan field SIMRS yang akan dibaca untuk pasien berobat.</li>
                        <li>Buat form input/edit rekap pasien.</li>
                        <li>Buat proses tarik data dari SIMRS ke form aplikasi.</li>
                    </ol>
                </section>
            </section>
        </main>
    </body>
</html>
