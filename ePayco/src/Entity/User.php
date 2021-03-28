<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User implements \JsonSerializable //configurar clase para que sea serializable atra vez de la interfaz
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="tipe_doc", type="string", length=50, nullable=false)
     */
    private $tipeDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="number_doc", type="string", length=255, nullable=false)
     */
    private $numberDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="current_timestamp()"})
     */
    private $createdAt = 'current_timestamp()';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTipeDoc(): ?string
    {
        return $this->tipeDoc;
    }

    public function setTipeDoc(string $tipeDoc): self
    {
        $this->tipeDoc = $tipeDoc;

        return $this;
    }

    public function getNumberDoc(): ?string
    {
        return $this->numberDoc;
    }

    public function setNumberDoc(string $numberDoc): self
    {
        $this->numberDoc = $numberDoc;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * devolver array cuando este clase se serializa 
     */
    public function jsonSerialize(): array{ 
        return[
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }

}
