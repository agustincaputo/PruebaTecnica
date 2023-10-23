<?php
namespace App\Services;
use App\Models\Farmacia;
use Illuminate\Support\Facades\Validator;

class FarmaciaService {
    public function __construct(Type $var = null) {
        $this->farmaciaModel = new Farmacia();
    }

    public function store(array $farmaciaData): Farmacia {
        $validator = Validator::make($farmaciaData,$this->farmaciaModel->getRules());
        if($validator->fails())
            throw new \Exception($validator->errors()->toJson());

        $farmacia = $this->farmaciaModel->create($farmaciaData);
        return $farmacia;
    }

    public function update(array $farmaciaData,int $id): Farmacia {
        $validator = Validator::make($farmaciaData,$this->farmaciaModel->getRules());
        if($validator->fails())
            throw new \Exception($validator->errors()->toJson());
        $this->farmaciaModel->where('id',$id)
                ->update($farmaciaData);
        $farmacia = $this->farmaciaModel->find($id);
        return $farmacia;
    }

    public function show(int $id): Farmacia {
        $farmacia = $this->farmaciaModel->find($id);
        return $farmacia;
    }

    public function index(array $filter) {
        return $this->farmaciaModel->paginate();
    }

    public function destroy(int $id) {
        $this->farmaciaModel->where('id',$id)
                            ->delete();
    }

    public function getDistances(int $lat, int $lon) {
        return $this->farmaciaModel->selectRaw("farmacia.*,SQRT(POWER(({$lat} - latitud),2) + POWER(({$lon} - longitud),2)) as distancia ")
                ->orderBy('distancia','asc')
                ->paginate();
    }

    public function getNearest(int $lat, int $lon): Farmacia {
        return $this->farmaciaModel->selectRaw("farmacia.*,SQRT(POWER(({$lat} - latitud),2) + POWER(({$lon} - longitud),2)) as distancia ")
                ->orderBy('distancia','asc')
                ->first();
    }
}
