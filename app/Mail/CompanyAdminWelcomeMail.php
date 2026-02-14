<p?hp

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyAdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $user;
    public $password;

    public function __construct(Company $company, User $user, $password)
    {
        $this->company = $company;
        $this->user = $user;
        $this->password = $password;
    }

    public function build()
    {
        return $this->markdown('emails.company-admin-welcome')
            ->subject('Welcome to BusSwift - Your Company Has Been Created');
    }
}
