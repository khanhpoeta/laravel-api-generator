namespace App\Dto\ApiDto;

use Illuminate\Http\Request;

use Spatie\DataTransferObject\FlexibleDataTransferObject;
use \Spatie\DataTransferObject\DataTransferObjectCollection;

use App\Dto\ModelDto\{{ $model_name }}Dto;

class {{ $action_name }}{{ $model_name }}ResponseDto extends {{ $model_name }}Dto
{
    public static function fromEntity(\App\Models\{{ $model_name }} $entity = null): ?self
    {
        if(is_null($entity)) return null;

        $result = new self($entity->attributesToArray());

        return $result;
    }
}

/**
 * @OA\Schema(
 *     schema="{{ $action_name }}{{ $model_name }}ApiResponse",
 *     type="object",
 *     title="{{ $action_name }}{{ $model_name }}ApiResponse",
 *     properties={
 *         @OA\Property(property="success", type="string"),
 *         @OA\Property(property="code", type="integer"),
 *         @OA\Property(property="locale", type="string"),
 *         @OA\Property(property="message", type="string"),
 *         @OA\Property(property="data", type="object", ref="#/components/schemas/{{ $model_name }}Dto"),
 *     }
 * )
 */