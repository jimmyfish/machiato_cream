<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * @ORM\Column(name="order_datetime", type="datetime")
     */
    private $orderDatetime;

    /**
     * @ORM\Column(name="total_order_value", type="integer")
     */
    private $totalOrderValue;

    /**
     * @ORM\Column(name="average_unit_price", type="float")
     */
    private $averageUnitPrice;

    /**
     * @ORM\Column(name="distinct_unit_count", type="integer")
     */
    private $distinctUnitCount;

    /**
     * @ORM\Column(name="total_units_count", type="integer")
     */
    private $totalUnitsCount;

    /**
     * @ORM\Column(name="batch_number", type="string")
     */
    private $batchNumber;

    /**
     * @ORM\Column(name="customer_state", type="string")
     */
    private $customerState;

    /**
     * @return mixed
     */
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }

    /**
     * @param mixed $batchNumber
     */
    public function setBatchNumber($batchNumber): void
    {
        $this->batchNumber = $batchNumber;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getOrderDatetime(): ?\DateTimeInterface
    {
        return $this->orderDatetime;
    }

    public function setOrderDatetime(\DateTimeInterface $orderDatetime): self
    {
        $this->orderDatetime = $orderDatetime;

        return $this;
    }

    public function getTotalOrderValue(): ?int
    {
        return $this->totalOrderValue;
    }

    public function setTotalOrderValue(int $totalOrderValue): self
    {
        $this->totalOrderValue = $totalOrderValue;

        return $this;
    }

    public function getAverageUnitPrice(): ?float
    {
        return $this->averageUnitPrice;
    }

    public function setAverageUnitPrice(float $averageUnitPrice): self
    {
        $this->averageUnitPrice = $averageUnitPrice;

        return $this;
    }

    public function getDistinctUnitCount(): ?int
    {
        return $this->distinctUnitCount;
    }

    public function setDistinctUnitCount(int $distinctUnitCount): self
    {
        $this->distinctUnitCount = $distinctUnitCount;

        return $this;
    }

    public function getTotalUnitsCount(): ?int
    {
        return $this->totalUnitsCount;
    }

    public function setTotalUnitsCount(int $totalUnitsCount): self
    {
        $this->totalUnitsCount = $totalUnitsCount;

        return $this;
    }

    public function getCustomerState(): ?string
    {
        return $this->customerState;
    }

    public function setCustomerState(string $customerState): self
    {
        $this->customerState = $customerState;

        return $this;
    }
}
