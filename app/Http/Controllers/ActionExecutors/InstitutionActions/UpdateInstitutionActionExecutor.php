<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 17:18:45
 */

namespace App\Http\Controllers\ActionExecutors\InstitutionActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Traits\MultimediaTrait;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageManagerInterface;

class UpdateInstitutionActionExecutor extends InstitutionActionExecutor
{
    use MultimediaTrait;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        ImageManagerInterface $imageManager
    ) {
        parent::__construct($orchestratorHandler);
        $this->setImageManager($imageManager);
    }

    /**
     * @param Request $request
     * @return Institution
     */
    public function invoke(Request $request): Institution
    {
        $dataUpdate = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'shortname' => $request->input('shortname'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
        ];

        $token = $request->input('token');
        if (isset($token)) {
            $filename = $this->saveImage($token);
            $dataUpdate['logo'] = $filename;
        }

        $request->merge(['dataUpdate' => json_encode($dataUpdate)]);
        return $this->orchestratorHandler->handler('update-institution', $request);
    }

    /**
     * @inheritDoc
     */
    public function canExecute(): string
    {
        return 'update-institution-action';
    }
}
