<?php


namespace Lifepet\Wallet\SDK\Domains;


use App\Integrations\Juno\Contracts\Arrayable;
use App\Integrations\Juno\Contracts\HasPaymentTypes;
use Carbon\Carbon;

class Charge extends Model implements HasPaymentTypes
{
    const TAX_TYPE__PERCENTAGE = 'PERCENTAGE';
    const TAX_TYPE__FIXED = 'FIXED';

    protected $description;
    protected $totalAmount;
    protected $installments;
    protected $dueDate;
    protected $pixKey;
    protected $pixIncludeImage;
    protected $references;
    protected $maxOverdueDays;
    protected $fine;
    protected $interest;
    protected $discountAmount;
    protected $discountDays;
    protected $paymentTypes;
    protected $paymentAdvice;

    /**
     * Charge constructor.
     * @param string $description
     * @param float $totalAmount
     * @param int $installments
     * @param Carbon $dueDate
     * @param array $paymentTypes
     * @param bool $pix
     */
    public function __construct(string $description, float $totalAmount, int $installments, Carbon $dueDate, array $paymentTypes = [], bool $pix = false)
    {
        $this->description = $description;
        $this->totalAmount = $totalAmount;
        $this->installments = $installments;
        $this->setDueDate($dueDate);
        $this->setPaymentTypes($paymentTypes);
        if($pix) {
            $this->setAsPixPayment();
            $this->pixIncludeImage = true;
            $this->pixKey = env("JUNO__RANDOM_PIX_KEY");
        }
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return int
     */
    public function getInstallments(): int
    {
        return $this->installments;
    }

    /**
     * @param int $installments
     */
    public function setInstallments(int $installments): void
    {
        $this->installments = $installments;
    }

    /**
     * @return string
     */
    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    /**
     * @param Carbon $dueDate
     */
    public function setDueDate(Carbon $dueDate): void
    {
        if(!$dueDate) {
            $dueDate = now();
        }

        $this->dueDate = $dueDate->format(self::DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getPixKey(): string
    {
        return $this->pixKey;
    }

    /**
     * @param string $pixKey
     */
    public function setPixKey(string $pixKey): void
    {
        $this->pixKey = $pixKey;
    }

    /**
     * @return bool
     */
    public function getPixIncludeImage(): bool
    {
        return $this->pixIncludeImage;
    }

    /**
     * @param bool $pixIncludeImage
     */
    public function setPixIncludeImage(bool $pixIncludeImage): void
    {
        $this->pixIncludeImage = $pixIncludeImage;
    }

    /**
     * @return array
     */
    public function getReferences(): array
    {
        return $this->references;
    }

    /**
     * @param array $references
     */
    public function setReferences(array $references): void
    {
        $this->references = $references;
    }

    /**
     * @return int
     */
    public function getMaxOverdueDays(): int
    {
        return $this->maxOverdueDays;
    }

    /**
     * @param int $maxOverdueDays
     */
    public function setMaxOverdueDays(int $maxOverdueDays): void
    {
        $this->maxOverdueDays = $maxOverdueDays;
    }

    /**
     * @return float
     */
    public function getFine(): float
    {
        return $this->fine;
    }

    /**
     * @param float $fine
     */
    public function setFine(float $fine): void
    {
        if($fine > 20) {
            $fine = 20;
        }
        if($fine < 0) {
            $fine = 0;
        }

        $this->fine = $fine;
    }

    /**
     * @return float
     */
    public function getInterest(): float
    {
        return $this->interest;
    }

    /**
     * @param float $interest
     */
    public function setInterest(float $interest): void
    {
        if($interest > 20) {
            $interest = 20;
        }
        if($interest < 0) {
            $interest = 0;
        }

        $this->interest = $interest;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @param float $discountAmount
     */
    public function setDiscountAmount(float $discountAmount): void
    {
        if($discountAmount < 0) {
            $discountAmount = 0;
        }
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return int
     */
    public function getDiscountDays(): int
    {
        return $this->discountDays;
    }

    /**
     * @param int $discountDays
     */
    public function setDiscountDays(int $discountDays): void
    {
        if($discountDays < 0) {
            $discountDays = 0;
        }
        $this->discountDays = $discountDays;
    }

    /**
     * @return array
     */
    public function getPaymentTypes(): array
    {
        return $this->paymentTypes;
    }

    /**
     * @param array $paymentTypes
     */
    public function setPaymentTypes(array $paymentTypes): void
    {
        $this->paymentTypes = $paymentTypes;
    }

    /**
     * @return bool
     */
    public function getPaymentAdvice(): bool
    {
        return $this->paymentAdvice;
    }

    /**
     * @param bool $paymentAdvice
     */
    public function setPaymentAdvice(bool $paymentAdvice): void
    {
        $this->paymentAdvice = $paymentAdvice;
    }

    public function setAsCreditCardPayment()
    {
        $this->paymentTypes = [self::PAYMENT_TYPE__CREDIT_CARD];
    }

    public function setAsPixPayment()
    {
        $this->paymentTypes = [self::PAYMENT_TYPE__BOLETO_PIX];
    }

    public function toArray(): array
    {
        $serialized = [
            'description' => $this->getDescription(),
            'installments' => $this->getInstallments(),
            'dueDate' => $this->getDueDate(),
            'paymentTypes' => $this->getPaymentTypes(),
        ];
        if($serialized['installments'] > 1) {
            $serialized['totalAmount'] = $this->getTotalAmount();
        } else {
            $serialized['amount'] = $this->getTotalAmount();
        }
        if($this->pixKey) {
            $serialized['pixKey'] = $this->pixKey;
            $serialized['pixIncludeImage'] = $this->pixIncludeImage;
        }
        return $serialized;
    }
}
