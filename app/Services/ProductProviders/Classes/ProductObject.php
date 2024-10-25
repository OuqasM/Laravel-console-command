<?php

namespace App\Services\ProductProviders\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductObject
{
    use HasFactory;

    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $sku;

    /**
     * @var float|null
     */
    private $price;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string|null
     */
    private $variations;

    /**
     * @var int|null
     */
    private $quantity;

    /**
     * @var string|null
     */
    private $status;


    public function __construct()
    {
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @param string|null $sku
     * @return $this
     */
    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     * @return $this
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVariations(): ?string
    {
        return $this->variations;
    }

    /**
     * @param string|null $variations
     * @return $this
     */
    public function setVariations(?string $variations): self
    {
        $this->variations = $variations;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return $this
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}

