<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 21:58:50
 */

namespace Core\Institution\Application\UseCases\UpdateInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Application\UseCases\UseCasesService;
use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Exception;

class UpdateInstitution extends UseCasesService
{
    public function __construct(InstitutionRepositoryContract $institutionRepository)
    {
        parent::__construct($institutionRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): Institution
    {
        $this->validateRequest($request, UpdateInstitutionRequest::class);

        /** @var UpdateInstitutionRequest $request */
        $institution = $this->institutionRepository->find($request->id());
        foreach ($request->data() as $field => $value) {
            $methodName = 'change'.ucfirst($field);

            if (is_callable([$this, $methodName])) {
                $institution = $this->{$methodName}($institution, $value);
            }
        }

        $institution->refreshSearch();

        return $this->institutionRepository->persistInstitution($institution);
    }

    private function changeCode(Institution $institution, string $code): Institution
    {
        $institution->code()->setValue($code);

        return $institution;
    }

    private function changeName(Institution $institution, string $name): Institution
    {
        $institution->name()->setValue($name);

        return $institution;
    }

    private function changeShortname(Institution $institution, string $shortname): Institution
    {
        $institution->shortname()->setValue($shortname);

        return $institution;
    }

    private function changeLogo(Institution $institution, string $logo): Institution
    {
        $institution->logo()->setValue($logo);

        return $institution;
    }

    private function changeObservations(Institution $institution, string $observations): Institution
    {
        $institution->observations()->setValue($observations);

        return $institution;
    }

    private function changeAddress(Institution $institution, string $address): Institution
    {
        $institution->address()->setValue($address);

        return $institution;
    }

    private function changePhone(Institution $institution, string $phone): Institution
    {
        $institution->contactCard()->phone()->setValue($phone);

        return $institution;
    }

    private function changeEmail(Institution $institution, string $email): Institution
    {
        $institution->contactCard()->email()->setValue($email);

        return $institution;
    }

    /**
     * @throws Exception
     */
    private function changeState(Institution $institution, int $state): Institution
    {
        $institution->state()->setValue($state);

        return $institution;
    }

    private function changeUpdatedAt(Institution $institution, \DateTime $dateTime): Institution
    {
        $institution->updatedAt()->setValue($dateTime);

        return $institution;
    }
}
