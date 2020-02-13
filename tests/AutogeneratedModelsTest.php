<?php

namespace Tests;

use Pagos360\Models\Account;
use Pagos360\Models\Adhesion;
use Pagos360\Models\CardAdhesion;
use Pagos360\Models\CardDebitRequest;
use Pagos360\Models\ChargebackData;
use Pagos360\Models\ChargebackReport;
use Pagos360\Models\CollectionData;
use Pagos360\Models\CollectionReport;
use Pagos360\Models\DebitRequest;
use Pagos360\Models\HolderData;
use Pagos360\Models\PaymentMetadata;
use Pagos360\Models\PaymentRequest;
use Pagos360\Models\Result;
use Pagos360\Models\SettlementData;
use Pagos360\Models\SettlementReport;
use Pagos360\Repositories\AbstractRepository;
use Pagos360\Repositories\AccountRepository;
use Pagos360\Repositories\AdhesionRepository;
use Pagos360\Repositories\CardAdhesionRepository;
use Pagos360\Repositories\CardDebitRequestRepository;
use Pagos360\Repositories\ChargebackDataRepository;
use Pagos360\Repositories\ChargebackReportRepository;
use Pagos360\Repositories\CollectionDataRepository;
use Pagos360\Repositories\CollectionReportRepository;
use Pagos360\Repositories\DebitRequestRepository;
use Pagos360\Repositories\HolderDataRepository;
use Pagos360\Repositories\PaymentMetadataRepository;
use Pagos360\Repositories\PaymentRequestRepository;
use Pagos360\Repositories\ResultRepository;
use Pagos360\Repositories\SettlementDataRepository;
use Pagos360\Repositories\SettlementReportRepository;

class AutogeneratedModelsTest extends AbstractTestCase
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            "Account" => [
                Account::class,
                AccountRepository::class,
            ],
            "Adhesion" => [
                Adhesion::class,
                AdhesionRepository::class,
            ],
            "CardAdhesion" => [
                CardAdhesion::class,
                CardAdhesionRepository::class,
            ],
            "CardDebitRequest" => [
                CardDebitRequest::class,
                CardDebitRequestRepository::class,
            ],
            "ChargebackData" => [
                ChargebackData::class,
                ChargebackDataRepository::class,
            ],
            "ChargebackReport" => [
                ChargebackReport::class,
                ChargebackReportRepository::class,
            ],
            "CollectionData" => [
                CollectionData::class,
                CollectionDataRepository::class,
            ],
            "CollectionReport" => [
                CollectionReport::class,
                CollectionReportRepository::class,
            ],
            "DebitRequest" => [
                DebitRequest::class,
                DebitRequestRepository::class,
            ],
            "HolderData" => [
                HolderData::class,
                HolderDataRepository::class,
            ],
            "PaymentMetadata" => [
                PaymentMetadata::class,
                PaymentMetadataRepository::class,
            ],
            "PaymentRequest" => [
                PaymentRequest::class,
                PaymentRequestRepository::class,
            ],
            "Result" => [
                Result::class,
                ResultRepository::class,
            ],
            "SettlementData" => [
                SettlementData::class,
                SettlementDataRepository::class,
            ],
            "SettlementReport" => [
                SettlementReport::class,
                SettlementReportRepository::class,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $model      Fully qualified name of the model.
     * @param string $repository Fully qualified name of the repository.
     */
    public function assertNoSettersForReadOnlyFields(
        string $model,
        string $repository
    ) {
        /** @var AbstractRepository $repository */
        $readonlyFields = array_filter(
            $repository::FIELDS,
            function ($field) {
                return $field[AbstractRepository::FLAG_READONLY] ?? false;
            }
        );

        foreach ($readonlyFields as $fieldKey => $fieldDefinition) {
            $this->assertFalse(
                method_exists(
                    $model,
                    'set' . ucfirst($fieldKey)
                ),
                "Model $model should not have a setter for attribute $fieldKey"
            );
        }
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $model      Fully qualified name of the model.
     * @param string $repository Fully qualified name of the repository.
     */
    public function assertMaybeFieldsCanReturnNull(
        string $model,
        string $repository
    ) {
        /** @var AbstractRepository $repository */
        $maybeFields = array_filter(
            $repository::FIELDS,
            function ($field) {
                $required = $field[AbstractRepository::FLAG_REQUIRED] ?? false;
                $readonly = $field[AbstractRepository::FLAG_READONLY] ?? false;
                $maybe = $field[AbstractRepository::FLAG_MAYBE] ?? false;
                return (!$readonly && !$required) || $maybe;
            }
        );
        if (count($maybeFields) === 0) {
            // This particular model doesn't have any MAYBE fields, so we return early
            $this->expectNotToPerformAssertions();
            return;
        }

        $getters = [];
        $input = [];
        foreach ($maybeFields as $fieldKey => $fieldDefinition) {
            $getters[] = 'get' . ucfirst($fieldKey);
        }

        $reflection = new $model($input);
        foreach ($getters as $getter) {
            $this->assertNull(
                $reflection->$getter(),
                "Getter in model $model should also return null"
            );
        }
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $model      Fully qualified name of the model.
     * @param string $repository Fully qualified name of the repository.
     */
    public function assertRequiredFieldsArentReadonly(
        string $model,
        string $repository
    ) {
        /** @var AbstractRepository $repository */
        foreach ($repository::FIELDS as $field => $definition) {
            $required = $definition[AbstractRepository::FLAG_REQUIRED] ?? false;
            $readonly = $definition[AbstractRepository::FLAG_READONLY] ?? false;
            $this->assertFalse(
                $required && $readonly,
                "Model $model declared field $field as readonly and required"
            );
        }
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $model      Fully qualified name of the model.
     * @param string $repository Fully qualified name of the repository.
     */
    public function assertAllDefinedFieldsAreInModel(
        string $model,
        string $repository
    ) {
        /** @var AbstractRepository $repository */
        foreach ($repository::FIELDS as $field => $definition) {
            $this->assertTrue(
                property_exists($model, $field),
                "Field $field is defined in $repository but not in model $model"
            );
        }
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param string $model      Fully qualified name of the model.
     * @param string $repository Fully qualified name of the repository.
     */
    public function assertAllModelPropertiesHaveAGetter(
        string $model,
        string $repository
    ) {
        /** @var AbstractRepository $repository */
        foreach ($repository::FIELDS as $field => $definition) {
            $getter = 'get' . ucfirst($field);
            $this->assertTrue(
                method_exists($model, $getter),
                "Property $field in model $model should have a getter"
//                property_exists(, $field),
//                "Field $field is defined in $repository but not in model $model"
            );
        }
    }
}
