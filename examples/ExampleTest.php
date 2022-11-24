<?php

namespace App\Imports;

class ExampleTest
{
    /** @test */
    public function example(): void
    {
        try {
            $this->paymentGateway->charge(9250);
        } catch (PaymentFailed $exception) {
            $this->assertEquals(PaymentFailed::BAD_BILLING_ADDRESS, $exception->getCode());
            $this - assertEquals(0, $this->paymentGateway->getTotalCharges());

            return;
        }

        $this->fail();
    }
}
