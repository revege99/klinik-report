<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_main_ui_pages_are_accessible(): void
    {
        $this->seed();

        $this->get(route('dashboard'))->assertOk()->assertSee('Dashboard Operasional');
        $this->get(route('input-data'))->assertOk()->assertSee('Input Data Pasien');
        $this->get(route('input-pengeluaran'))->assertOk()->assertSee('Input Pengeluaran');
        $this->get(route('rekap-bulanan'))->assertOk()->assertSee('Rekap Bulanan');
        $this->get(route('rekap-tahunan'))->assertOk()->assertSee('Rekap Tahunan');
    }
}
