<?php

namespace WechatMiniProgramDeliveryReturnBundle\Request;

/**
 * 地址对象
 */
class AddressObject
{
    /**
     * @var string 收件人
     */
    private string $name;

    /**
     * @var string 联系电话
     */
    private string $mobile;

    /**
     * @var string 国家
     */
    private string $country = '中国';

    /**
     * @var string 省份
     */
    private string $province;

    /**
     * @var string 城市
     */
    private string $city;

    /**
     * @var string 区县
     */
    private string $area;

    /**
     * @var string 详细地址
     */
    private string $address;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getArea(): string
    {
        return $this->area;
    }

    public function setArea(string $area): void
    {
        $this->area = $area;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public static function fromArray(array $item): static
    {
        $obj = new self();
        $obj->setName($item['name']);
        $obj->setMobile($item['mobile']);
        $obj->setCountry($item['country']);
        $obj->setProvince($item['province']);
        $obj->setCity($item['city']);
        $obj->setArea($item['area']);
        $obj->setAddress($item['address']);

        return $obj;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'mobile' => $this->getMobile(),
            'country' => $this->getCountry(),
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'area' => $this->getArea(),
            'address' => $this->getAddress(),
        ];
    }
}
