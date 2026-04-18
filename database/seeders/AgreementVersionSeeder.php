<?php

namespace Database\Seeders;

use App\Models\AgreementVersion;
use Illuminate\Database\Seeder;

class AgreementVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $content = <<<HTML
<h1>SOFTWARE SERVICE AGREEMENT</h1>
<p>This Agreement is entered into between Provider of Fleet & Rental Management Software and the Client.</p>

<h3>1. Services</h3>
<p>The Provider agrees to provide access to the Fleet Rental ERP platform including:</p>
<ul>
    <li>Fleet Management</li>
    <li>GPS Tracking</li>
    <li>Accounting Intelligence</li>
    <li>Maintenance Monitoring</li>
    <li>Risk Analytics</li>
    <li>AI Pricing Engine</li>
</ul>

<h3>2. Subscription & Payment</h3>
<p>Client agrees to pay the agreed monthly or annual subscription fee. Failure to pay may result in:</p>
<ul>
    <li>Account suspension</li>
    <li>Data freeze</li>
    <li>Limited access</li>
</ul>

<h3>3. Data Ownership</h3>
<p>All operational data entered by the Client remains property of the Client. Provider shall not sell or misuse client data.</p>

<h3>4. Liability</h3>
<p>The system provides predictive analytics and risk indicators. The Provider is not liable for mechanical breakdown, driver negligence, or financial losses due to operational decisions.</p>

<h3>5. Termination</h3>
<p>Either party may terminate with 30 days notice.</p>

<h3>6. Digital Acceptance</h3>
<p>By signing electronically, the Client confirms acceptance of all terms.</p>
HTML;

        AgreementVersion::updateOrCreate(
            ['version' => '1.0'],
            [
                'content' => trim($content),
                'active' => true,
            ]
        );
    }
}
