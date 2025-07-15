<?php
namespace App\Api\Dto\Notification;

use App\Enum\NotificationType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

//DTO for sending notification to front, grouping friend rq and invitations together
class NotificationDto
{
    #[Groups(['notification:read'])]
    #[Assert\NotNull]
    public NotificationType $type;

    #[Groups(['notification:read'])]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id;

    #[Groups(['notification:read'])]
    #[Assert\NotNull]
    #[Assert\Type(\DateTimeImmutable::class)]
    public \DateTimeImmutable $sendAt;

    #[Groups(['notification:read'])]
    #[Assert\Type('array')]
    public array $data = [];

    public function __construct(
        NotificationType $type,
        string $id,
        \DateTimeImmutable $sendAt,
        array $data = []
    ) {
        $this->type = $type;
        $this->id = $id;
        $this->sendAt = $sendAt;
        $this->data = $data;
    }
}
