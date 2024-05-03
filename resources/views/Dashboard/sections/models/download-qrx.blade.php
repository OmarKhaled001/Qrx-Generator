<div class="modal fade" id="downloadModal{{$qrxCode->id}}" tabindex="-1" aria-labelledby="downloadModal{{$qrxCode->id}}Label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="downloadModal{{$qrxCode->id}}Label">Download {{$qrxCode->name}}</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{route('dashboard.qr.download')}}" method="get" class="p-1">
          @csrf
          <div class="modal-body">
            <div class="row">
                <div class="col">
                    <label for="name" class="mr-sm-2">Name :</label>
                    <input id="id" type="hidden" name="id" class="form-control" value="{{$qrxCode->id}}">
                    <input id="name" type="text" name="name" class="form-control" value="{{$qrxCode->name}}">
                    @error('name') <p class="text-danger">{{$message}}</p> @enderror
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="type">Type:</label>
                <select name="type" class="select2 form-select mt-1" id="type">
                    <option selected value="png">Png</option>
                    <option value="svg">SVG</option>
                    <option value="eps">Eps</option>
                </select>
                @error('type') <p class="text-danger">{{$message}}</p> @enderror
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success">Download</button>
          </div>
      </form>
      </div>
    </div>
  </div>