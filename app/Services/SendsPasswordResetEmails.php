<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\Setup\UserEmails;
use Illuminate\Support\Facades\Artisan;

trait SendsPasswordResetEmails
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);



        //For MultiAuth here we Reconnecting System Dyanamically(Custome Change)
        $this->databaseConnectByEmail($request->email);



        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        
        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse($response)
    {
        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(
            ['email' => trans($response)]
        );
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('hrms');
    }

    public function databaseConnectByEmail($email){
        $user = UserEmails::where('email',$email)
            ->join('configs','configs.id','=','user_emails.config_id')
            ->first();

        if(count($user)){
            Session(['database'=>$user->database_name, 'config_id' => $user->config_id]);
            Artisan::call("db:connect", ['database'=> $user->database_name]);
//            echo \DB::connection()->getDatabaseName();
            return true;
        }else{
            return false;
        }
    }
}
