<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 08:36:36
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use App\Traits\UtilsDateTimeTrait;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;

class CreateCampusOrchestrator extends CampusOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(CampusManagementContract $campusManagement)
    {
        parent::__construct($campusManagement);
    }

    /**
     * @param Request $request
     * @return Campus
     */
    public function make(Request $request): Campus
    {
        $dataCampus = [
            'id' => $request->input('campusId'),
            'institutionId' => $request->input('institutionId'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
            'state' => ValueObjectStatus::STATE_NEW,
            'createdAt' => $this->getCurrentTime()->format(self::DATE_FORMAT)
        ];

        return $this->campusManagement->createCampus([Campus::TYPE => $dataCampus]);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'create-campus';
    }
}
