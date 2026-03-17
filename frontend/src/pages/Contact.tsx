import { useState } from 'react';
import { MapPin, Phone, Mail, FileBadge, Send, Loader2 } from 'lucide-react';
import Header from '../components/Header';
import Footer from '../components/Footer';

export default function Contact() {
  const [formData, setFormData] = useState({ name: '', email: '', phone: '', message: '' });
  const [status, setStatus] = useState<{type: 'idle' | 'loading' | 'success' | 'error', message: string}>({ type: 'idle', message: '' });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData(prev => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus({ type: 'loading', message: 'Sending message...' });
    
    try {
      const apiUrl = import.meta.env.VITE_API_BASE_URL || '';
      const response = await fetch(`${apiUrl}/contact/submit`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });
      
      const data = await response.json().catch(() => null);
      
      if (response.ok && (!data || data.status === 'success' || data.success)) {
        setStatus({ type: 'success', message: 'Message sent successfully! We will get back to you soon.' });
        setFormData({ name: '', email: '', phone: '', message: '' });
      } else {
        setStatus({ type: 'error', message: data?.message || 'Failed to send message. Please try again.' });
      }
    } catch (err) {
      console.error(err);
      setStatus({ type: 'error', message: 'An error occurred. Please try again later.' });
    }
  };

  return (
    <div className="min-h-screen bg-[#f3f6f9] font-sans text-slate-900 flex flex-col">
      {/* Header */}
      <Header />

      {/* Main Content */}
      <main className="flex-1 py-12 px-4">
        <div className="container mx-auto max-w-6xl">
           <div className="text-center mb-12 sm:mb-16">
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-[#1a233a] mb-4">Contact Us</h1>
              <p className="text-lg text-slate-600 max-w-2xl mx-auto">Have a question about our products, wholesale pricing, or bulk orders? We'd love to hear from you!</p>
           </div>

           <div className="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start mb-16">
              {/* Contact Information */}
              <div className="bg-[#12182b] text-white rounded-3xl p-8 sm:p-10 lg:p-12 shadow-2xl relative overflow-hidden h-full flex flex-col justify-center">
                <div className="absolute top-0 right-0 w-[400px] h-[400px] bg-indigo-900/30 rounded-full blur-[80px]"></div>
                
                <h2 className="text-3xl font-serif font-bold mb-10 relative z-10 text-yellow-400">Get In Touch</h2>
                
                <div className="space-y-8 relative z-10 flex-1">
                  <div className="flex items-start gap-5">
                    <div className="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center shrink-0 mt-1">
                       <MapPin className="w-6 h-6 text-yellow-500" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold mb-1">Our Store Address</h3>
                        <p className="text-slate-300 leading-relaxed font-light">
                        392/2, Sarada College Road,<br />
                        Autostand, South Alagapuram, Salem -<br />
                        636016
                        </p>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-5">
                    <div className="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                       <Phone className="w-6 h-6 text-yellow-500" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold mb-1">Phone Number</h3>
                        <p className="text-slate-300 font-light">+91 {import.meta.env.VITE_WHATSAPP_NUMBER?.replace(/^91/, '') || '80987 60720'} <span className="text-xs ml-2 text-yellow-500 font-bold">(WhatsApp Available)</span></p>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-5">
                    <div className="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <Mail className="w-6 h-6 text-yellow-500" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold mb-1">Email Address</h3>
                        <p className="text-slate-300 font-light">nssapparelstirupur@gmail.com</p>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-5">
                    <div className="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <FileBadge className="w-6 h-6 text-yellow-500" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold mb-1">Tax Information</h3>
                        <p className="text-slate-300 uppercase tracking-widest font-light text-sm">GSTIN: 33CESPJ5443N1Z1</p>
                    </div>
                  </div>
                </div>

                <div className="mt-12 relative z-10 border-t border-white/20 pt-8 flex flex-col gap-4">
                     <p className="text-slate-400 font-light text-sm">We are open Monday to Saturday from 10:00 AM to 8:00 PM.</p>
                     <a href={`https://wa.me/${import.meta.env.VITE_WHATSAPP_NUMBER || '918098760720'}?text=Hello%20NSS%20APPARELS%20I%20want%20catalog`} target="_blank" rel="noopener noreferrer" className="bg-white/10 hover:bg-white/20 text-white font-bold py-3 px-6 rounded-lg transition-colors border border-white/20 text-center flex items-center justify-center gap-2">
                         <Phone className="w-5 h-5" /> WHATSAPP US
                     </a>
                </div>
              </div>

              {/* Contact Form */}
              <div className="bg-white rounded-3xl p-8 sm:p-10 lg:p-12 shadow-xl border border-slate-100 h-full">
                 <h2 className="text-3xl font-serif font-bold mb-2 text-[#1a233a]">Send us a message</h2>
                 <p className="text-slate-500 mb-8">Fill out the form below and our team will get back to you shortly.</p>

                 {status.type === 'success' && (
                    <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
                        {status.message}
                    </div>
                 )}
                 {status.type === 'error' && (
                    <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium">
                        {status.message}
                    </div>
                 )}

                 <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <label htmlFor="name" className="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value={formData.name}
                            onChange={handleChange}
                            required
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-[#1a233a] focus:border-transparent transition-all font-medium"
                            placeholder="John Doe"
                        />
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label htmlFor="email" className="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email"
                                value={formData.email}
                                onChange={handleChange} 
                                required
                                className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-[#1a233a] focus:border-transparent transition-all font-medium"
                                placeholder="john@example.com"
                            />
                        </div>
                        <div>
                            <label htmlFor="phone" className="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                value={formData.phone}
                                onChange={handleChange}
                                required
                                className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-[#1a233a] focus:border-transparent transition-all font-medium"
                                placeholder="+91 XXXXX XXXXX"
                            />
                        </div>
                    </div>
                    <div>
                        <label htmlFor="message" className="block text-sm font-bold text-slate-700 mb-2">Your Message</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            value={formData.message}
                            onChange={handleChange}
                            required
                            rows={4}
                            className="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-[#1a233a] focus:border-transparent transition-all font-medium resize-none"
                            placeholder="How can we help you?"
                        ></textarea>
                    </div>

                    <button 
                        type="submit" 
                        disabled={status.type === 'loading'}
                        className="w-full bg-[#1a233a] hover:bg-[#12182b] text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 transition-colors focus:ring-4 focus:ring-[#1a233a]/30 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        {status.type === 'loading' ? (
                            <><Loader2 className="w-5 h-5 animate-spin" /> Sending...</>
                        ) : (
                            <>Send Message <Send className="w-5 h-5 ml-1" /></>
                        )}
                    </button>
                 </form>
              </div>
           </div>

           {/* Map Section */}
           <div className="bg-white rounded-3xl p-2 sm:p-4 shadow-xl border border-slate-100 overflow-hidden h-[400px] mb-8">
                <iframe 
                    title="NSS Apparels Location"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1m3!1d3907.4589006900456!2d78.1408800152912!3d11.662058045431602!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTHCsDM5JzQzLjQiTiA3OMKwMDgnMzUuMSJF!5e0!3m2!1sen!2sin!4v1650000000000!5m2!1sen!2sin" 
                    width="100%" 
                    height="100%" 
                    style={{ border: 0, borderRadius: '1.25rem' }} 
                    allowFullScreen={true} 
                    loading="lazy" 
                    referrerPolicy="no-referrer-when-downgrade"
                ></iframe>
           </div>

        </div>
      </main>

      {/* Footer */}
      <Footer />
    </div>
  );
}
