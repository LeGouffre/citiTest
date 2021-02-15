<?php

namespace App\Entity;

use App\Repository\TokenNumberRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenNumberRepository::class)
 */
class TokenNumber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $UpdateAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasSee = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $testedBy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $seeAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUsed = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $activateBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $testedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->UpdateAt;
    }

    public function setUpdateAt(\DateTimeInterface $UpdateAt): self
    {
        $this->UpdateAt = $UpdateAt;

        return $this;
    }

    public function getHasSee(): ?bool
    {
        return $this->hasSee;
    }

    public function setHasSee(bool $hasSee): self
    {
        $this->hasSee = $hasSee;

        return $this;
    }

    public function getTestedBy(): ?User
    {
        return $this->testedBy;
    }

    public function setTestedBy(?User $testedBy): self
    {
        $this->testedBy = $testedBy;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getSeeAt(): ?\DateTimeInterface
    {
        return $this->seeAt;
    }

    public function setSeeAt(?\DateTimeInterface $seeAt): self
    {
        $this->seeAt = $seeAt;

        return $this;
    }

    public function getIsUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isUsed): self
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    public function getSerializeToken()
    {
        return [
            "id" => $this->id,
            "number" => $this->number,
            "result" => $this->result,
            "location" => $this->location,
            'ActivateAt' =>$this->UpdateAt ? $this->UpdateAt->format("Y-D-M h:m:s") : null,
            "ActivateBy" => $this->activateBy ? [
                "id" => $this->testedBy->getId(),
                "email" => $this->testedBy->getEmail(),
                "userName" => $this->testedBy->getUsername()
            ] : [],
            "testedBy" => $this->testedBy ? [
                "id" => $this->testedBy->getId(),
                "email" => $this->testedBy->getEmail(),
                "userName" => $this->testedBy->getUsername()
            ] : [],
            "testedAt" => $this->testedAt ? $this->testedAt->format("Y-D-M h:m:s") : null,
            "hasSee" => $this->hasSee,
            "seeAt" => $this->seeAt ? $this->seeAt->format("Y-D-M h:m:s") : null
        ];
    }

    public function getActivateBy(): ?User
    {
        return $this->activateBy;
    }

    public function setActivateBy(?User $activateBy): self
    {
        $this->activateBy = $activateBy;

        return $this;
    }

    public function getTestedAt(): ?\DateTimeInterface
    {
        return $this->testedAt;
    }

    public function setTestedAt(?\DateTimeInterface $testedAt): self
    {
        $this->testedAt = $testedAt;

        return $this;
    }
}
