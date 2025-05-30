<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;



class ClientController extends Controller
{
    //

    public function index()
    {
        return view('clients.index');
    }

    public function store(Request $req)
    {
        // Step 1: Validate fields
        $validator = Validator::make($req->all(), [
            'client_name'      => 'required|string|max:255',
            'client_country'   => 'required|string|max:255',
            'client_email'     => 'required|email|unique:clients,client_email',
            'client_manager'   => 'required|string|max:255',
            'client_phoneno'   => 'required|numeric|digits_between:9,15|unique:clients,client_phoneno',
            'client_whatsapp'  => 'required|numeric|digits_between:9,15|unique:clients,client_whatsapp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation Error',
                'error'   => $validator->errors()
            ], 422);
        }

        // Step 2: Create and save client
        $client = new Client();
        $client->client_name     = $req->client_name;
        $client->client_country  = $req->client_country;
        $client->client_email    = $req->client_email;
        $client->client_manager  = $req->client_manager;
        $client->client_phoneno  = $req->client_phoneno;
        $client->client_whatsapp = $req->client_whatsapp;
        $client->user_id         = auth()->user()->id;

        if ($client->save()) {
            return response()->json([
                'success' => 1,
                'message' => 'Client Created Successfully'
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'An error occurred while saving the client.'
            ]);
        }
    }

    public function getClients()
    {
        return datatables(Client::with('user')->latest())->make(true);
    }


    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }


    public function update(Request $req, $id)
    {
        $client = Client::findOrFail($id);

        $validator = Validator::make($req->all(), [
            'client_name' => 'required',
            'client_country' => 'required',
            'client_email' => 'required|email|unique:clients,client_email,' . $id,
            'client_manager' => 'required',
            'client_phoneno' => 'required|numeric|digits_between:9,15|unique:clients,client_phoneno,' . $id,
            'client_whatsapp' => 'required|numeric|digits_between:9,15|unique:clients,client_whatsapp,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => 0,
                "error" => $validator->errors()
            ], 422);
        }

        $client->update($req->only([
            'client_name', 'client_country', 'client_email',
            'client_manager', 'client_phoneno', 'client_whatsapp'
        ]));

        return response()->json([
            "success" => 1,
            "message" => "Client updated successfully"
        ]);
    }


    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['success' => 1, 'message' => 'Client deleted successfully']);
    }


     public function download()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }




}
