import { useEffect, useState } from 'react';
import { Truck, MapPin, Phone, Mail, FileBadge, ChevronRight, MessageCircle, Star, Package, Image as ImageIcon } from 'lucide-react';
import { Link } from 'react-router-dom';
import Header from '../components/Header';
import Footer from '../components/Footer';

export default function Home() {
  const [categories, setCategories] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const apiUrl = import.meta.env.VITE_API_BASE_URL || '';
    fetch(`${apiUrl}/api/categories`)
      .then(res => res.json())
      .then(data => {
        if (data && data.length > 0) {
          setCategories(data);
        }
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        setLoading(false);
      });
  }, []);

  return (
    <div className="min-h-screen bg-[#f3f6f9] font-sans text-slate-900 flex flex-col">
      
      {/* Header */}
      <Header />

      <style>
        {`
          @keyframes pattern-move {
            0% { background-position: 0 0; }
            100% { background-position: 300px 300px; }
          }
          @keyframes title-glow {
            0%, 100% { text-shadow: 0 0 15px rgba(255,255,255,0.4), 0 0 30px rgba(255,255,255,0.2); }
            50% { text-shadow: 0 0 25px rgba(255,255,255,0.6), 0 0 40px rgba(255,255,255,0.4); }
          }
          @keyframes text-shine {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
          }
          @keyframes text-gradient-move {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
          }
        `}
      </style>
      
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-[#121b2d] pt-16 sm:pt-24 pb-24 sm:pb-36 px-4 border-b-4 border-[#d4af37]">
        
        {/* Animated Background Pattern */}
        <div 
          className="absolute inset-0 z-0 pointer-events-none opacity-[0.06] invert"
          style={{ 
            backgroundImage: "url('/logo.png')", 
            backgroundSize: '300px',
            backgroundRepeat: 'repeat',
            animation: 'pattern-move 30s linear infinite'
          }}
        ></div>

        {/* Diagonal Light Beam effect */}
        <div className="absolute inset-0 z-0 bg-gradient-to-tr from-[#1a233a]/40 via-blue-400/10 to-[#1a233a]/80 pointer-events-none"></div>
        <div className="absolute top-0 right-0 w-96 h-96 bg-blue-500/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div className="relative z-10 max-w-4xl mx-auto flex flex-col items-center text-center">
          <h1 
            className="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black mb-4 tracking-wider uppercase drop-shadow-[0_0_15px_rgba(212,175,55,0.4)]" 
            style={{ 
                background: 'linear-gradient(to right, #ffffff 20%, #ff8c00 40%, #d4af37 60%, #ffffff 80%)',
                backgroundSize: '200% auto',
                color: '#fff',
                backgroundClip: 'text',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                animation: 'text-gradient-move 3s linear infinite'
            }}
          >
            NSS APPARELS
          </h1>
          <p 
            className="text-xl sm:text-3xl font-light mb-8 sm:mb-12 tracking-wide font-serif drop-shadow-md"
            style={{ 
                background: 'linear-gradient(to right, #fde047 20%, #f97316 50%, #fde047 80%)',
                backgroundSize: '200% auto',
                color: '#fff',
                backgroundClip: 'text',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                animation: 'text-gradient-move 4s linear infinite'
            }}
          >
            Wholesale Dry Fit T-Shirts, Tracks & Shorts
          </p>
          
          <div className="bg-white/10 backdrop-blur-md shadow-2xl rounded-full px-5 sm:px-8 py-4 sm:py-5 flex flex-wrap justify-center items-center gap-4 sm:gap-8 border border-white/20">
            <div className="flex items-center gap-2">
              <div className="w-6 h-6 bg-[#d4af37] rounded-sm flex items-center justify-center transform rotate-45 shadow-[0_0_10px_rgba(212,175,55,0.5)]">
                <div className="w-2 h-2 bg-white rounded-full"></div>
              </div>
              <span className="font-bold text-xs sm:text-sm text-white uppercase tracking-widest">NEW ARRIVALS</span>
            </div>
            
            <div className="hidden sm:block w-px h-6 bg-white/20"></div>
            
            <div className="flex items-center gap-2">
              <Star className="w-5 h-5 sm:w-6 sm:h-6 text-[#d4af37] fill-[#d4af37] drop-shadow-[0_0_8px_rgba(212,175,55,0.6)]" />
              <span className="font-bold text-xs sm:text-sm text-white uppercase tracking-widest">BEST SELLING</span>
            </div>

            <div className="hidden sm:block w-px h-6 bg-white/20"></div>

            <div className="flex items-center gap-2">
              <Package className="w-5 h-5 sm:w-6 sm:h-6 text-[#d4af37] drop-shadow-[0_0_8px_rgba(212,175,55,0.6)]" />
              <span className="font-bold text-xs sm:text-sm text-white uppercase tracking-widest">BULK OFFERS</span>
            </div>
          </div>
        </div>
      </section>

      {/* Explore Products */}
      <section className="relative -mt-10 sm:-mt-16 z-20 px-4 max-w-7xl mx-auto" id="products">
        <h2 
            className="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-center mb-10 sm:mb-12"
            style={{
                background: 'linear-gradient(to right, #1a233a 20%, #d4af37 40%, #1a233a 60%, #1a233a 80%)',
                backgroundSize: '200% auto',
                color: '#000',
                backgroundClip: 'text',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                animation: 'text-shine 3s linear infinite',
                textShadow: "0 4px 6px rgba(0,0,0,0.1)"
            }}
        >
            Explore Our Products
        </h2>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          {loading ? (
             <>
                {[...Array(3)].map((_, idx) => (
                  <div key={idx} className="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col items-center pt-8 pb-6 px-4 border border-slate-100 animate-pulse">
                     <div className="h-8 bg-slate-200 rounded-md w-1/2 mb-6"></div>
                     <div className="w-full h-40 sm:h-48 mb-6 bg-slate-200 rounded-xl"></div>
                     <hr className="w-full border-slate-200 mb-6" />
                     <div className="w-full h-12 sm:h-14 bg-slate-200 rounded-md"></div>
                  </div>
                ))}
             </>
          ) : categories.length === 0 ? (
             <div className="col-span-full text-center py-10 font-bold text-slate-500">No categories found.</div>
          ) : (
             categories.map((cat, idx) => {
               // Get image
               let imgUrl = cat.image;
               if (!imgUrl && cat.products && cat.products.length > 0 && cat.products[0].main_image) {
                 imgUrl = cat.products[0].main_image;
               }

               return (
                 <div key={idx} className="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col items-center pt-8 pb-6 px-4 border border-slate-100 hover:-translate-y-1 transition-transform">
                   <h3 className="text-2xl font-bold text-[#1a233a] mb-6 capitalize">{cat.name}</h3>
                   
                   <div className="w-full h-40 sm:h-48 mb-6 overflow-hidden relative flex items-center justify-center bg-slate-50 rounded">
                     {imgUrl ? (
                        <img src={imgUrl} className="max-h-full max-w-full object-contain drop-shadow-md" alt={cat.name} />
                     ) : (
                        <ImageIcon className="w-12 h-12 text-slate-300" />
                     )}
                   </div>
                   
                   <hr className="w-full border-slate-200 mb-6" />
                   
                   <Link to={`/products/${cat.id}`} className="w-full bg-[#1a233a] hover:bg-slate-800 text-white font-bold py-3 sm:py-4 rounded-md flex items-center justify-center gap-2 transition-colors text-lg">
                     VIEW ALL <ChevronRight className="w-5 h-5" />
                   </Link>
                 </div>
               );
             })
          )}
        </div>
        
        <div className="mt-12 text-center">
          <Link to="/products" className="inline-flex items-center gap-2 bg-[#d4af37] hover:bg-[#b09028] text-white font-bold py-4 px-10 rounded-full transition-transform hover:-translate-y-1 shadow-lg shadow-yellow-500/30 text-lg">
             VIEW ALL PRODUCTS <ChevronRight className="w-6 h-6" />
          </Link>
        </div>
      </section>

      {/* WhatsApp Button Section */}
      <section className="py-12 sm:py-16 px-4 flex justify-center">
        <a href={`https://wa.me/${import.meta.env.VITE_WHATSAPP_NUMBER || '918098760720'}?text=Hello%20NSS%20APPARELS%20I%20want%20catalog`} target="_blank" rel="noopener noreferrer" className="bg-gradient-to-b from-[#fde047] to-[#eab308] hover:to-[#ca8a04] px-8 sm:px-12 py-3 sm:py-4 rounded-xl border border-yellow-500 shadow-[0_4px_14px_0_rgba(234,179,8,0.39)] flex items-center gap-3 transform transition-transform hover:scale-105 active:scale-95">
          <MessageCircle className="w-6 h-6 sm:w-8 sm:h-8 text-green-700 fill-green-500" />
          <span className="text-[#1a233a] font-bold text-xl sm:text-2xl">Browse WhatsApp Catalog</span>
        </a>
      </section>

      {/* Features Row */}
      <section className="max-w-6xl mx-auto px-4 border-t border-b border-slate-300 py-6 sm:py-8 mb-12 sm:mb-20">
        <div className="flex flex-col md:flex-row justify-center items-center gap-6 md:gap-12 lg:gap-24">
          <div className="flex items-center gap-3">
            <Truck className="w-8 h-8 sm:w-10 sm:h-10 text-[#1a233a]" />
            <span className="text-lg sm:text-xl font-bold text-[#1a233a]">All Tamilnadu Shipping</span>
          </div>
          
          <div className="hidden md:block w-px h-10 bg-slate-300"></div>
          
          <div className="flex items-center gap-3">
            <div className="text-yellow-500">
              <Package className="w-8 h-8 sm:w-10 sm:h-10 fill-yellow-500" />
            </div>
            <span className="text-lg sm:text-xl font-bold text-[#1a233a]">Best Wholesale Price</span>
          </div>
          
          <div className="hidden md:block w-px h-10 bg-slate-300"></div>
          
          <div className="flex items-center gap-3">
            <div className="text-amber-600">
              <Package className="w-8 h-8 sm:w-10 sm:h-10 fill-amber-500 text-amber-600" />
            </div>
            <span className="text-lg sm:text-xl font-bold text-[#1a233a]">Bulk Orders Available</span>
          </div>
        </div>
      </section>

      {/* Visit Our Store */}
      <section className="px-4 max-w-7xl mx-auto mb-16" id="contact">
        <div className="bg-[#12182b] text-white rounded-[2rem] p-8 md:p-14 shadow-2xl relative overflow-hidden">
          <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-900/20 rounded-full blur-[80px]"></div>
          
          <div className="grid md:grid-cols-2 gap-10 md:gap-16 relative z-10 items-center">
             <div>
              <h2 className="text-3xl sm:text-4xl font-serif font-bold mb-8 md:mb-12">Visit Our Store</h2>
              
              <div className="space-y-6 sm:space-y-8">
                <div className="flex items-start gap-4">
                  <MapPin className="w-6 h-6 text-yellow-500 shrink-0 mt-1" />
                  <p className="text-sm sm:text-base text-slate-300 leading-relaxed font-light">
                    392/2, Sarada College Road,<br />
                    Autostand, South Alagapuram, Salem -<br />
                    636016
                  </p>
                </div>
                
                <div className="flex items-center gap-4">
                  <Phone className="w-6 h-6 text-yellow-500 shrink-0" />
                  <p className="text-sm sm:text-base text-slate-300 font-light">+91 {import.meta.env.VITE_WHATSAPP_NUMBER?.replace(/^91/, '') || '80987 60720'}</p>
                </div>
                
                <div className="flex items-center gap-4">
                  <Mail className="w-6 h-6 text-yellow-500 shrink-0" />
                  <p className="text-sm sm:text-base text-slate-300 font-light">nssapparelstirupur@gmail.com</p>
                </div>
                
                <div className="flex items-center gap-4">
                  <FileBadge className="w-6 h-6 text-yellow-500 shrink-0" />
                  <p className="text-sm sm:text-base text-slate-300 uppercase tracking-widest font-light text-xs sm:text-sm">GSTIN : 33CESPJ5443N1Z1</p>
                </div>
              </div>
            </div>
            
             <div className="h-64 sm:h-80 md:h-full rounded-2xl overflow-hidden shadow-inset border border-slate-700/50 relative">
              <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=800&auto=format&fit=crop" alt="Store Interior" className="w-full h-full object-cover" />
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <Footer />

    </div>
  );
}
