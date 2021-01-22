<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 */
class Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="float")
     */
    private $area;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $constructionDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $floor;

    /**
     * @ORM\Column(type="integer")
     */
    private $floors;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\Column(type="integer")
     */
    private $bedrooms;

    /**
     * @ORM\Column(type="integer")
     */
    private $bathrooms;

    /**
     * @ORM\Column(type="integer")
     */
    private $toilets;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFurnished;

    /**
     * @ORM\Column(type="boolean")
     */
    private $containsStorage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isKitchenSeparated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $containDiningRoom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ground;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $heater;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fireplace;

    /**
     * @ORM\Column(type="boolean")
     */
    private $elevator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $externalStorage;

    /**
     * @ORM\Column(type="float")
     */
    private $areaExternalStorage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $guarding;

    /**
     * @ORM\Column(type="integer")
     */
    private $energyConsumption;

    /**
     * @ORM\Column(type="integer")
     */
    private $gasEmissions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rentOrSale;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     */
    private $charges;

    /**
     * @ORM\Column(type="float")
     */
    private $guarentee;

    /**
     * @ORM\Column(type="float")
     */
    private $feesPrice;

    /**
     * @ORM\Column(type="float")
     */
    private $inventoryPrice;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="properties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="property", orphanRemoval=true)
     */
    private $contacts;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Favorite::class, mappedBy="property", orphanRemoval=true)
     */
    private $favorites;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getArea(): ?float
    {
        return $this->area;
    }

    public function setArea(float $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getConstructionDate(): ?\DateTimeInterface
    {
        return $this->constructionDate;
    }

    public function setConstructionDate(\DateTimeInterface $constructionDate): self
    {
        $this->constructionDate = $constructionDate;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getFloors(): ?int
    {
        return $this->floors;
    }

    public function setFloors(int $floors): self
    {
        $this->floors = $floors;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): self
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(int $bathrooms): self
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getToilets(): ?int
    {
        return $this->toilets;
    }

    public function setToilets(int $toilets): self
    {
        $this->toilets = $toilets;

        return $this;
    }

    public function getIsFurnished(): ?bool
    {
        return $this->isFurnished;
    }

    public function setIsFurnished(bool $isFurnished): self
    {
        $this->isFurnished = $isFurnished;

        return $this;
    }

    public function getContainsStorage(): ?bool
    {
        return $this->containsStorage;
    }

    public function setContainsStorage(bool $containsStorage): self
    {
        $this->containsStorage = $containsStorage;

        return $this;
    }

    public function getIsKitchenSeparated(): ?bool
    {
        return $this->isKitchenSeparated;
    }

    public function setIsKitchenSeparated(bool $isKitchenSeparated): self
    {
        $this->isKitchenSeparated = $isKitchenSeparated;

        return $this;
    }

    public function getContainDiningRoom(): ?bool
    {
        return $this->containDiningRoom;
    }

    public function setContainDiningRoom(bool $containDiningRoom): self
    {
        $this->containDiningRoom = $containDiningRoom;

        return $this;
    }

    public function getGround(): ?string
    {
        return $this->ground;
    }

    public function setGround(string $ground): self
    {
        $this->ground = $ground;

        return $this;
    }

    public function getHeater(): ?string
    {
        return $this->heater;
    }

    public function setHeater(string $heater): self
    {
        $this->heater = $heater;

        return $this;
    }

    public function getFireplace(): ?bool
    {
        return $this->fireplace;
    }

    public function setFireplace(bool $fireplace): self
    {
        $this->fireplace = $fireplace;

        return $this;
    }

    public function getElevator(): ?bool
    {
        return $this->elevator;
    }

    public function setElevator(bool $elevator): self
    {
        $this->elevator = $elevator;

        return $this;
    }

    public function getExternalStorage(): ?string
    {
        return $this->externalStorage;
    }

    public function setExternalStorage(string $externalStorage): self
    {
        $this->externalStorage = $externalStorage;

        return $this;
    }

    public function getAreaExternalStorage(): ?float
    {
        return $this->areaExternalStorage;
    }

    public function setAreaExternalStorage(float $areaExternalStorage): self
    {
        $this->areaExternalStorage = $areaExternalStorage;

        return $this;
    }

    public function getGuarding(): ?bool
    {
        return $this->guarding;
    }

    public function setGuarding(bool $guarding): self
    {
        $this->guarding = $guarding;

        return $this;
    }

    public function getEnergyConsumption(): ?int
    {
        return $this->energyConsumption;
    }

    public function setEnergyConsumption(int $energyConsumption): self
    {
        $this->energyConsumption = $energyConsumption;

        return $this;
    }

    public function getGasEmissions(): ?int
    {
        return $this->gasEmissions;
    }

    public function setGasEmissions(int $gasEmissions): self
    {
        $this->gasEmissions = $gasEmissions;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getRentOrSale(): ?string
    {
        return $this->rentOrSale;
    }

    public function setRentOrSale(string $rentOrSale): self
    {
        $this->rentOrSale = $rentOrSale;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCharges(): ?float
    {
        return $this->charges;
    }

    public function setCharges(float $charges): self
    {
        $this->charges = $charges;

        return $this;
    }

    public function getGuarentee(): ?float
    {
        return $this->guarentee;
    }

    public function setGuarentee(float $guarentee): self
    {
        $this->guarentee = $guarentee;

        return $this;
    }

    public function getFeesPrice(): ?float
    {
        return $this->feesPrice;
    }

    public function setFeesPrice(float $feesPrice): self
    {
        $this->feesPrice = $feesPrice;

        return $this;
    }

    public function getInventoryPrice(): ?float
    {
        return $this->inventoryPrice;
    }

    public function setInventoryPrice(float $inventoryPrice): self
    {
        $this->inventoryPrice = $inventoryPrice;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setProperty($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getProperty() === $this) {
                $contact->setProperty(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setProperty($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getProperty() === $this) {
                $favorite->setProperty(null);
            }
        }

        return $this;
    }
}
