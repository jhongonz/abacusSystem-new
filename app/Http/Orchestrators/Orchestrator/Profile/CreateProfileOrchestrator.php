<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 00:15:58
 */

namespace App\Http\Orchestrators\Orchestrator\Profile;

use App\Traits\UtilsDateTimeTrait;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;

class CreateProfileOrchestrator extends ProfileOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(
        ProfileManagementContract $profileManagement
    ) {
        parent::__construct($profileManagement);
    }

    /**
     * @param Request $request
     * @return Profile
     */
    public function make(Request $request): Profile
    {
        $dataProfile = [
            'id' => null,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'modulesAggregator' => json_decode($request->input('modulesAggregator'), true),
            'state' => ValueObjectStatus::STATE_NEW,
            'createdAt' => $this->getCurrentTime()
        ];

        return $this->profileManagement->createProfile($dataProfile);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'create-profile';
    }
}
