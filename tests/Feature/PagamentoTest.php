<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PagamentoTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

//test_pagamento_mock_aprovado_updates_pedido_status
//test_pagamento_mock_recusado_keeps_pedido_status
//test_pagamento_registers_gateway_payload
//test_cannot_pay_already_paid_pedido
