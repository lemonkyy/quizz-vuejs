<?php
namespace App\Api\Dto\Notification;

use App\Enum\NotificationType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

//DTO for sending notification to front, grouping friend rq and invitations together
class NotificationOutputDto
{
    #[Assert\NotNull]
    public NotificationType $type;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id;

    #[Assert\NotNull]
    #[Assert\Type(\DateTimeImmutable::class)]
    public \DateTimeImmutable $sentAt;

    #[Assert\Type('array')]
    public array $data = [];

    public function __construct(
        NotificationType $type,
        string $id,
        \DateTimeImmutable $sentAt,
        array $data = []
    ) {
        $this->type = $type;
        $this->id = $id;
        $this->sentAt = $sentAt;
        $this->data = $data;
    }
}
