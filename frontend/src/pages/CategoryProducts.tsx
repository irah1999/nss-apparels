import { useEffect, useState, useRef, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ChevronRight, PackageSearch, Image as ImageIcon, Search, Loader2 } from 'lucide-react';
import { motion } from 'framer-motion';
import Header from '../components/Header';
import Footer from '../components/Footer';

export default function CategoryProducts() {
  const { id } = useParams(); // if id is present, show products. else show categories.
  
  const [items, setItems] = useState<any[]>([]);
  const [categoryInfo, setCategoryInfo] = useState<any>(null); // Only used when showing products for a category
  
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  
  const [search, setSearch] = useState('');
  
  const observer = useRef<IntersectionObserver | null>(null);
  const abortControllerRef = useRef<AbortController | null>(null);

  const fetchItems = async (pageNumber: number, isNewSearch = false) => {
    try {
      if (isNewSearch) setLoading(true);
      else setLoadingMore(true);

      // Cancel any previous pending requests if typing too fast triggers.
      if (abortControllerRef.current) {
         abortControllerRef.current.abort();
      }
      abortControllerRef.current = new AbortController();

      const searchParams = new URLSearchParams({
        page: pageNumber.toString(),
        limit: '10'
      });
      
      const trimmedSearch = search.trim();
      if (trimmedSearch) searchParams.append('search', trimmedSearch);

      const apiUrl = import.meta.env.VITE_API_BASE_URL || '';
      let url = '';
      
      // Standard layout: If id exists, it is product list. If not, it is Category list.
      if (id) {
          searchParams.append('category_id', id);
          url = `${apiUrl}/api/products?${searchParams.toString()}`;
      } else {
          url = `${apiUrl}/api/categories_paginated?${searchParams.toString()}`;
      }

      const res = await fetch(url, { signal: abortControllerRef.current.signal });
      const data = await res.json();
      
      const newItems = data && Array.isArray(data.data) ? data.data : [];

      if (isNewSearch) {
        setItems(newItems);
      } else {
        setItems(prev => [...prev, ...newItems]);
      }
      
      setHasMore(data.has_more);
      setPage(data.page);
    } catch (err: any) {
      if (err.name !== 'AbortError') {
         console.error(err);
      }
    } finally {
      setLoading(false);
      setLoadingMore(false);
    }
  };

  // Fetch category info once if ID is present
  useEffect(() => {
    if (id) {
       const apiUrl = import.meta.env.VITE_API_BASE_URL || '';
       fetch(`${apiUrl}/api/categories`)
        .then(res => res.json())
        .then(data => {
            const cat = data.find((c: any) => c.id == id);
            if (cat) setCategoryInfo(cat);
        })
        .catch(console.error);
    } else {
        setCategoryInfo(null);
    }
  }, [id]);

  // Refetch when search or id changes
  useEffect(() => {
    setPage(1);
    setHasMore(true);
    fetchItems(1, true);
  }, [id, search]);

  // Infinite Scroll Observer setup
  const lastElementRef = useCallback((node: any) => {
    if (loading || loadingMore) return;
    if (observer.current) observer.current.disconnect();
    
    observer.current = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting && hasMore) {
        fetchItems(page + 1, false);
      }
    });
    
    if (node) observer.current.observe(node);
  }, [loading, loadingMore, hasMore, page, search, id]);

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearch(e.target.value);
  };

  return (
    <div className="min-h-screen bg-[#f3f6f9] font-sans text-slate-900 flex flex-col">
      {/* Header */}
      <Header />

      {/* Main Content */}
      <main className="flex-1 py-8 px-4">
        <div className="container mx-auto max-w-7xl">
          {/* Breadcrumb / Header */}
          <motion.div 
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.5 }}
            className="mb-6 pl-2 border-l-4 border-yellow-500"
          >
             <div className="flex items-center gap-2 text-sm text-slate-500 mb-2">
                 <Link to="/" className="hover:text-amber-600 font-medium">Home</Link>
                 <ChevronRight className="w-4 h-4" />
                 <span className="font-bold text-slate-800">{id ? (categoryInfo ? categoryInfo.name : 'Category') : 'All Categories'}</span>
             </div>
             <h1 className="text-3xl md:text-4xl font-serif font-bold text-[#1a233a]">
                 {id ? (categoryInfo ? categoryInfo.name : 'Products') : 'Explore Categories'}
             </h1>
          </motion.div>

          {/* Search Toolbar */}
          <motion.div 
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-8 flex flex-col sm:flex-row gap-4 justify-between items-center"
          >
             <div className="relative w-full sm:w-96">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5" />
                <input 
                  type="text" 
                  placeholder={id ? "Search products..." : "Search categories..."}
                  value={search}
                  onChange={handleSearch}
                  className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all font-medium"
                />
             </div>
          </motion.div>

          <div className="flex flex-col gap-8">
            <div className="flex-1">
               {loading ? (
                  <div className={id ? "grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6" : "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"}>
                    {[...Array(id ? 8 : 6)].map((_, idx) => (
                       <div key={idx} className="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col gap-4 animate-pulse">
                          <div className={id ? "bg-slate-200 rounded-xl aspect-[3/4] w-full" : "bg-slate-200 rounded-xl h-48 w-full"}></div>
                          <div className="h-6 bg-slate-200 rounded-md w-3/4"></div>
                          {id && <div className="h-4 bg-slate-200 rounded-md w-1/2"></div>}
                       </div>
                    ))}
                  </div>
               ) : items.length === 0 ? (
                  <div className="text-center py-20 bg-white rounded-2xl border border-slate-200 shadow-sm">
                      <PackageSearch className="w-16 h-16 text-slate-300 mx-auto mb-4" />
                      <h3 className="text-xl font-bold text-slate-700">No {id ? 'products' : 'categories'} found</h3>
                      <p className="text-sm text-slate-500 mt-1">Try searching with a different keyword!</p>
                  </div>
               ) : (
                  <>
                     {id ? (
                        // Rendering Products
                        <div className="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                            {Array.isArray(items) && items.map((prod, index) => {
                                if (!prod) return null;
                                let addl: string[] = [];
                                try {
                                    if(prod.additional_images) addl = JSON.parse(prod.additional_images);
                                } catch(e){}
                                const hoverImg = addl && addl.length > 0 ? addl[0] : prod.main_image;

                                let colors: string[] = [];
                                try {
                                    if(prod.colors) colors = JSON.parse(prod.colors);
                                } catch(e){}

                                const isLastItem = index === items.length - 1;

                                return (
                                    <motion.div
                                      key={prod.id}
                                      initial={{ opacity: 0, scale: 0.95, y: 15 }}
                                      whileInView={{ opacity: 1, scale: 1, y: 0 }}
                                      viewport={{ once: true, margin: "-20px" }}
                                      transition={{ duration: 0.4, delay: (index % 4) * 0.1 }}
                                      ref={isLastItem ? lastElementRef : null}
                                      className="h-full"
                                    >
                                        <Link 
                                          to={`/product-detail/${prod.id}`} 
                                          className="h-full bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between border border-slate-100"
                                        >
                                            <div>
                                                <div className="aspect-[3/4] bg-slate-50 flex items-center justify-center overflow-hidden relative">
                                                    {prod.main_image ? (
                                                        <>
                                                            <img 
                                                              src={prod.main_image} 
                                                              onLoad={(e) => e.currentTarget.classList.remove('opacity-0')}
                                                              className="absolute inset-0 w-full h-full object-contain p-2 opacity-0 transition-opacity duration-500 group-hover:opacity-0 group-hover:scale-105" style={{ zIndex: 2 }} alt={prod.name} />
                                                            <img 
                                                              src={hoverImg} 
                                                              onLoad={(e) => e.currentTarget.classList.remove('opacity-0')}
                                                              className="absolute inset-0 w-full h-full object-contain p-2 opacity-0 transition-opacity duration-500 group-hover:opacity-100 group-hover:scale-110" style={{ zIndex: 1 }} alt={prod.name} />
                                                        </>
                                                    ) : (
                                                        <ImageIcon className="w-10 h-10 text-slate-300" />
                                                    )}
                                                </div>
                                                <div className="p-4 border-t border-slate-100 bg-white relative z-10">
                                                    <h4 className="font-bold text-[15px] sm:text-base text-[#1a233a] truncate group-hover:text-blue-600 transition-colors" title={prod.name}>{prod.name}</h4>
                                                    <p className="text-[11px] sm:text-xs text-slate-500 truncate mt-1">{prod.description || 'Premium wear'}</p>
                                                </div>
                                            </div>

                                            <div className="px-4 pb-4">
                                                {colors && colors.length > 0 && (
                                                    <div className="flex items-center gap-1.5 mt-2 mb-3">
                                                        {colors.slice(0, 4).map((c, i) => (
                                                            <span key={i} className="w-3.5 h-3.5 rounded-full border border-slate-200 shadow-sm" style={{ backgroundColor: c }}></span>
                                                        ))}
                                                    </div>
                                                )}
                                                <div className="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                                                    <span className="text-[#1a233a] font-bold text-xs uppercase">View Details</span>
                                                    <div className="p-1.5 bg-yellow-100 text-yellow-600 rounded-lg group-hover:scale-110 group-hover:bg-yellow-400 group-hover:text-white transition-all">
                                                        <ChevronRight className="w-4 h-4" />
                                                    </div>
                                                </div>
                                            </div>
                                        </Link>
                                    </motion.div>
                                );
                            })}
                        </div>
                     ) : (
                        // Rendering Categories
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                            {Array.isArray(items) && items.map((cat, index) => {
                                if (!cat) return null;
                                let imgUrl = cat.image;
                                if (!imgUrl && cat.products && cat.products.length > 0 && cat.products[0].main_image) {
                                  imgUrl = cat.products[0].main_image;
                                }

                                const isLastItem = index === items.length - 1;

                                return (
                                  <motion.div 
                                    key={cat.id} 
                                    ref={isLastItem ? lastElementRef : null} 
                                    initial={{ opacity: 0, y: 30 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, margin: "-20px" }}
                                    transition={{ duration: 0.5, delay: (index % 3) * 0.1 }}
                                    className="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col items-center pt-8 pb-6 px-4 border border-slate-100 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 group"
                                  >
                                    <h3 className="text-2xl font-bold text-[#1a233a] mb-6 capitalize">{cat.name}</h3>
                                    
                                    <div className="w-full h-40 sm:h-48 mb-6 overflow-hidden relative flex items-center justify-center bg-slate-50 rounded-xl">
                                      {imgUrl ? (
                                         <img src={imgUrl} className="max-h-full max-w-full object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-500" alt={cat.name} />
                                      ) : (
                                         <ImageIcon className="w-12 h-12 text-slate-300" />
                                      )}
                                    </div>
                                    
                                    <hr className="w-full border-slate-200 mb-6" />
                                    
                                    <Link to={`/products/${cat.id}`} className="w-full bg-[#1a233a] hover:bg-slate-800 text-white font-bold py-3 sm:py-4 rounded-md flex items-center justify-center gap-2 mb-3 transition-colors text-lg">
                                      VIEW {cat.name} <ChevronRight className="w-5 h-5" />
                                    </Link>
                                  </motion.div>
                                );
                            })}
                        </div>
                     )}

                    {/* Loading indicator for infinite scroll */}
                    {loadingMore && (
                        <div className="flex justify-center mt-8 py-4">
                            <Loader2 className="w-8 h-8 animate-spin text-blue-600" />
                        </div>
                    )}
                  </>
               )}
            </div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <Footer />
    </div>
  );
}
