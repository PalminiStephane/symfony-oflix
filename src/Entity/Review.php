<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 * 
 * @ORM\HasLifecycleCallbacks()
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * 
     * @Assert\NotBlank
     * @Assert\Length(max=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank
     */
    private $content;

    /**
     * @ORM\Column(type="float")
     * 
     * @Assert\NotBlank
     */
    private $rating;

    /**
     * @ORM\Column(type="json")
     * 
     * @Assert\NotBlank
     */
    private $reactions = [];

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Assert\NotBlank
     */
    private $watchedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Movie::class)
     */
    private $movie;

    /**
     * Avant d'ajouter le review dans la BDD
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        // TODO : recalcul du rating du film lié à mon review
        // je récupère la liste des reviews du film : nbReview
        // je récupère le rating actuel du film : rating
        // j'ai le rating de mon review

        // cumul des notes divisé par le nombre de notes

        // ! on est complètement hors scope de notre entité
        // ce n'est pas comme ça qu'il faut faire ce genre de calcul

    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReactions(): ?array
    {
        return $this->reactions;
    }

    public function setReactions(array $reactions): self
    {
        $this->reactions = $reactions;

        return $this;
    }

    public function getWatchedAt(): ?\DateTimeImmutable
    {
        return $this->watchedAt;
    }

    public function setWatchedAt(\DateTimeImmutable $watchedAt): self
    {
        $this->watchedAt = $watchedAt;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }
}