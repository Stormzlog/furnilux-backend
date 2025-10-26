const express = require('express');
const axios = require('axios');
const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Initialize Paystack transaction
app.post('/initialize-transaction', async (req, res) => {
  try {
    const { email, amount, currency, firstName, lastName, phone } = req.body;
    const reference = `FURNILUX-${Date.now()}-${Math.random().toString(36).substring(7)}`;

    console.log('Initializing transaction at', new Date().toLocaleString('en-US', { timeZone: 'Africa/Lagos' }), { email, amount, currency, reference });

    const response = await axios.post(
      'https://api.paystack.co/transaction/initialize',
      {
        email,
        amount: Math.round(amount * 100), // Convert to cents
        currency,
        reference,
        metadata: {
          custom_fields: [
            { display_name: 'Customer Name', variable_name: 'full_name', value: `${firstName} ${lastName}` },
            { display_name: 'Phone Number', variable_name: 'phone', value: phone }
          ]
        },
        callback_url: 'https://your-back4app-subdomain.back4app.io/thank-you.html' // Update later
      },
      {
        headers: {
          Authorization: 'Bearer YOUR_PAYSTACK_SECRET_KEY', // Replace with your secret key
          'Content-Type': 'application/json'
        }
      }
    );

    console.log('Paystack response:', response.data);
    res.json({
      access_code: response.data.data.access_code,
      reference
    });
  } catch (error) {
    console.error('Initialization error:', error.response?.data || error.message);
    res.status(500).json({ error: error.response?.data?.message || 'Failed to initialize transaction' });
  }
});

// Optional: Verify transaction
app.get('/verify-transaction', async (req, res) => {
  const { reference } = req.query;
  try {
    const response = await axios.get(`https://api.paystack.co/transaction/verify/${reference}`, {
      headers: {
        Authorization: 'Bearer YOUR_PAYSTACK_SECRET_KEY' // Replace with secret key
      }
    });
    console.log('Verification response:', response.data);
    res.json(response.data);
  } catch (error) {
    console.error('Verification error:', error.response?.data || error.message);
    res.status(500).json({ error: 'Failed to verify transaction' });
  }
});

// Export for Back4app
module.exports = app;