import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ChevronRight, Image as ImageIcon, MoveRight, X } from 'lucide-react';
import Header from '../components/Header';
import Footer from '../components/Footer';

export default function ProductDetail() {
  const { id } = useParams();
  const [product, setProduct] = useState<any>(null);
  const [category, setCategory] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [mainImage, setMainImage] = useState<string>('');
  
  // Modal state
  const [isModalOpen, setIsModalOpen] = useState(false);

  useEffect(() => {
    const apiUrl = import.meta.env.VITE_API_BASE_URL || '';
    fetch(`${apiUrl}/api/product/${id}`)
      .then(res => res.json())
      .then(data => {
        if (!data.error && data.product) {
          setProduct(data.product);
          setCategory(data.category);
          setMainImage(data.product.main_image);
        }
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        setLoading(false);
      });
  }, [id]);

  let additionalImages: string[] = [];
  if (product && product.additional_images) {
    try {
      additionalImages = JSON.parse(product.additional_images);
    } catch(e) {}
  }

  const allImages = [product?.main_image, ...additionalImages].filter(Boolean);

  let colors: string[] = [];
  try {
    if (product?.colors) colors = JSON.parse(product.colors);
  } catch(e) {}

  let sizes: string[] = [];
  try {
    if (product?.sizes) sizes = JSON.parse(product.sizes);
  } catch(e) {}

  const whatsappNum = import.meta.env.VITE_WHATSAPP_NUMBER || '918098760720';
  const orderMessage = `Hi NSS APPARELS, I am interested in ordering this product:%0A%0A*Name:* ${product?.name}%0A*Product ID:* ${product?.id}%0A*Category:* ${category?.name}%0A%0APlease provide me with the wholesale price and bulk ordering details.`;
  const whatsappUrl = `https://wa.me/${whatsappNum}?text=${orderMessage}`;


  return (
    <div className="min-h-screen bg-[#f3f6f9] font-sans text-slate-900 flex flex-col relative">
       {/* Header */}
       <Header />

      {/* Main Content */}
      <main className="flex-1 py-12 px-4 relative z-0">
        <div className="container mx-auto max-w-6xl">
          {loading ? (
             <div className="bg-white rounded-[2rem] shadow-xl overflow-hidden p-6 md:p-10 mb-16 border border-slate-100 flex flex-col md:flex-row gap-10 lg:gap-16 animate-pulse">
                {/* Image Skeleton */}
                <div className="w-full md:w-1/2 flex flex-col gap-4 max-w-lg mx-auto md:mx-0">
                    <div className="bg-slate-200 w-full aspect-square md:aspect-[4/5] rounded-2xl"></div>
                    <div className="grid grid-cols-4 sm:grid-cols-5 gap-3">
                        {[...Array(4)].map((_, i) => <div key={i} className="aspect-square bg-slate-200 rounded-xl"></div>)}
                    </div>
                </div>
                {/* Details Skeleton */}
                <div className="w-full md:w-1/2 flex flex-col justify-center gap-6">
                    <div className="h-6 w-24 bg-slate-200 rounded-full"></div>
                    <div className="h-12 w-3/4 bg-slate-200 rounded-lg"></div>
                    <div className="h-20 w-full bg-slate-200 rounded-xl"></div>
                    <div className="h-32 w-full bg-slate-200 rounded-xl"></div>
                    <div className="h-14 w-48 bg-slate-200 rounded-xl mt-8"></div>
                </div>
             </div>
          ) : !product ? (
             <div className="text-center py-20 font-bold text-slate-500">Product not found.</div>
          ) : (
            <>
              {/* Breadcrumb */}
              <div className="mb-8 border-l-4 border-yellow-500 pl-3">
                 <div className="flex items-center gap-2 text-sm text-slate-500 flex-wrap">
                     <Link to="/" className="hover:text-amber-600 font-medium whitespace-nowrap">Home</Link>
                     <ChevronRight className="w-4 h-4" />
                     {category && (
                         <>
                            <Link to={`/products/${category.id}`} className="hover:text-amber-600 font-medium whitespace-nowrap">{category.name}</Link>
                            <ChevronRight className="w-4 h-4" />
                         </>
                     )}
                     <span className="font-bold text-[#1a233a] text-ellipsis overflow-hidden">{product.name}</span>
                 </div>
              </div>

              {/* Product Info */}
              <div className="bg-white rounded-[2rem] shadow-xl overflow-hidden p-6 md:p-10 mb-16 border border-slate-100 flex flex-col md:flex-row gap-10 lg:gap-16">
                 {/* Images */}
                 <div className="w-full md:w-1/2 flex flex-col gap-4 max-w-lg mx-auto md:mx-0">
                    <div 
                      className="bg-slate-50 w-full aspect-square md:aspect-[4/5] rounded-2xl overflow-hidden border border-slate-200 flex items-center justify-center relative p-4 shadow-inner cursor-pointer hover:shadow-lg transition-all group"
                      onClick={() => setIsModalOpen(true)}
                    >
                        <span className="absolute top-4 left-4 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg z-10">BEST SELLER</span>
                        {mainImage ? (
                            <img src={mainImage} className="max-w-full max-h-full object-contain drop-shadow-xl group-hover:scale-105 transition-transform duration-500 cursor-zoom-in" alt={product.name} />
                        ) : (
                            <ImageIcon className="w-20 h-20 text-slate-300" />
                        )}
                        <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-center justify-center pointer-events-none">
                            <span className="bg-white/90 text-slate-800 text-sm font-bold px-4 py-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0">
                                Click to enlarge
                            </span>
                        </div>
                    </div>
                    
                    {allImages.length > 1 && (
                        <div className="grid grid-cols-4 sm:grid-cols-5 gap-3">
                            {allImages.map((img, idx) => (
                                <button key={idx} onClick={() => setMainImage(img)} className={`aspect-square bg-slate-50 rounded-xl overflow-hidden border-2 p-1 border-slate-200 hover:border-[#1a233a] transition-all ${mainImage === img ? 'ring-2 ring-[#d4af37] ring-offset-2 border-[#1a233a]' : ''}`}>
                                    <img src={img} className="w-full h-full object-contain drop-shadow" alt="" />
                                </button>
                            ))}
                        </div>
                    )}
                 </div>

                 {/* Details */}
                 <div className="w-full md:w-1/2 flex flex-col justify-center">
                    <div className="mb-2">
                        <span className="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">{category ? category.name : 'Product'}</span>
                    </div>
                    <h1 className="text-3xl md:text-5xl font-bold font-serif text-[#1a233a] mb-6 leading-tight">
                        {product.name}
                    </h1>

                    {(product.item_code || product.fabric || product.gsm) && (
                        <div className="bg-slate-50 border border-slate-100 p-4 rounded-xl mb-6 shadow-inner text-sm md:text-base text-slate-700">
                             <div className="flex font-semibold">
                                {product.item_code && <span className="mr-6"><span className="text-slate-500 font-normal">Code:</span> {product.item_code}</span>}
                                {product.fabric && <span className="mr-6"><span className="text-slate-500 font-normal">Fabric:</span> {product.fabric}</span>}
                                {product.gsm && <span><span className="text-slate-500 font-normal">GSM:</span> {product.gsm}</span>}
                            </div>
                        </div>
                    )}

                    <div className="prose prose-slate prose-sm md:prose-base dark:prose-invert mb-8 text-slate-600 leading-relaxed font-light">
                        {product.description || 'Premium quality apparel perfect for your collection. Order now for the best wholesales prices.'}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10 pb-10 border-b border-slate-200">
                        {/* Colors */}
                        {colors && colors.length > 0 && (
                            <div>
                                <h3 className="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wider">Available Colors</h3>
                                <div className="flex flex-wrap gap-2.5">
                                    {colors.map((c, i) => (
                                        <div key={i} className="w-8 h-8 rounded-full shadow-md border-2 border-white ring-1 ring-slate-200 cursor-pointer hover:scale-110 transition-transform" style={{ backgroundColor: c }} title={c}></div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Sizes */}
                        {sizes && sizes.length > 0 && (
                            <div>
                                <h3 className="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wider">Available Sizes</h3>
                                <div className="flex flex-wrap gap-2">
                                    {sizes.map((s, i) => (
                                        <div key={i} className="bg-white border border-slate-200 px-3 py-1.5 rounded-md text-sm font-bold text-slate-700 shadow-sm min-w-10 text-center uppercase">
                                            {s}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    <a href={whatsappUrl} target="_blank" rel="noopener noreferrer" className="bg-[#1a233a] hover:bg-[#12182b] text-white text-lg font-bold py-4 sm:py-5 px-8 rounded-xl shadow-lg shadow-slate-900/20 flex justify-center items-center gap-3 transition-colors mb-4 w-full md:w-auto self-start border-l-4 border-yellow-500">
                         ORDER VIA WHATSAPP <MoveRight className="w-5 h-5" />
                    </a>
                    <p className="text-xs text-slate-400">* Minimum order quantities may apply for wholesale pricing.</p>
                 </div>
              </div>
            </>
          )}
        </div>
      </main>

       {/* Footer */}
       <Footer />

      {/* Image Modal Lightbox */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/90 backdrop-blur-sm p-4 animate-in fade-in duration-300">
            <button 
              className="absolute top-6 right-6 lg:top-10 lg:right-10 text-white bg-white/10 hover:bg-white/20 p-3 rounded-full transition-colors z-50"
              onClick={() => setIsModalOpen(false)}
            >
              <X className="w-8 h-8" />
            </button>
            
            <div className="w-full max-w-5xl h-full max-h-[80vh] flex items-center justify-center relative">
                {mainImage ? (
                    <img src={mainImage} className="max-w-full max-h-full object-contain drop-shadow-2xl rounded-sm" alt="Fullscreen view" />
                ) : (
                    <ImageIcon className="w-20 h-20 text-slate-600" />
                )}
            </div>

            {/* Thumbnail Navigation inside Modal */}
            {allImages.length > 1 && (
                <div className="absolute bottom-6 left-0 right-0 flex justify-center overflow-x-auto gap-3 px-4 py-2">
                    <div className="flex gap-3 bg-black/50 p-3 rounded-2xl backdrop-blur-md border border-white/10">
                        {allImages.map((img, idx) => (
                            <button 
                                key={idx} 
                                onClick={() => setMainImage(img)} 
                                className={`h-16 w-16 md:h-20 md:w-20 flex-shrink-0 bg-white/5 rounded-xl overflow-hidden border-2 p-0.5 hover:border-white transition-all ${mainImage === img ? 'ring-2 ring-yellow-400 ring-offset-2 ring-offset-black border-yellow-400 opacity-100' : 'border-transparent opacity-60 hover:opacity-100'}`}
                            >
                                <img src={img} className="w-full h-full object-cover rounded-lg" alt="" />
                            </button>
                        ))}
                    </div>
                </div>
            )}
        </div>
      )}
    </div>
  );
}
