<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:23:06
 */

namespace Core\Campus\Application\UseCases\UpdateCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Application\UseCases\UseCasesService;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusRepositoryContract;

class UpdateCampus extends UseCasesService
{
    public function __construct(CampusRepositoryContract $campusRepository)
    {
        parent::__construct($campusRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): Campus
    {
        $this->validateRequest($request, UpdateCampusRequest::class);

        /** @var UpdateCampusRequest $request */
        $campus = $this->campusRepository->find($request->id());
        foreach ($request->data() as $field => $value) {
            $methodName = 'change'.ucfirst($field);

            if (is_callable([$this, $methodName])) {
                $campus = $this->{$methodName}($campus, $value);
            }
        }

        $campus->refreshSearch();
        return $this->campusRepository->persistCampus($campus);
    }

    private function changeInstitutionId(Campus $campus, int $institutionId): Campus
    {
        $campus->institutionId()->setValue($institutionId);
        return $campus;
    }

    private function changeName(Campus $campus, string $name): Campus
    {
        $campus->name()->setValue($name);
        return $campus;
    }

    private function changeAddress(Campus $campus, ?string $address): Campus
    {
        $campus->address()->setValue($address);
        return $campus;
    }

    private function changePhone(Campus $campus, ?string $phone): Campus
    {
        $campus->phone()->setValue($phone);
        return $campus;
    }

    private function changeEmail(Campus $campus, ?string $email): Campus
    {
        $campus->email()->setValue($email);
        return $campus;
    }

    private function changeObservations(Campus $campus, ?string $observations): Campus
    {
        $campus->observations()->setValue($observations);
        return $campus;
    }

    private function changeSearch(Campus $campus, ?string $search): Campus
    {
        $campus->search()->setValue($search);
        return $campus;
    }

    /**
     * @throws \Exception
     */
    private function changeState(Campus $campus, int $state): Campus
    {
        $campus->state()->setValue($state);
        return $campus;
    }

    private function changeCreatedAt(Campus $campus, \DateTime $dateTime): Campus
    {
        $campus->createdAt()->setValue($dateTime);
        return $campus;
    }

    private function changeUpdatedAt(Campus $campus, ?\DateTime $dateTime): Campus
    {
        if (! is_null($dateTime)) {
            $campus->updatedAt()->setValue($dateTime);
        }

        return $campus;
    }
}
