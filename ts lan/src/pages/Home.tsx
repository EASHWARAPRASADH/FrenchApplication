import React, { useState } from 'react';
import { motion } from 'motion/react';
import { 
  Globe, 
  Users, 
  Star, 
  Quote, 
  ChevronRight, 
  CheckCircle2, 
  GraduationCap, 
  Building2, 
  Smartphone
} from 'lucide-react';
import { Link } from 'react-router-dom';
import { FrenchQuiz } from '../components/FrenchQuiz';
import logo from '../assets/logo.jpg';
import arun from '../assets/arun.png';
import akansha from '../assets/akansha.jpg';
import cyrille from '../assets/cyrille.png';
import discoverFrance1 from '../assets/discover-france-1.jpeg';
import discoverFrance2 from '../assets/discover-france-2.jpeg';
import discoverFrance3 from '../assets/discover-france-3.jpeg';
import discoverFrance4 from '../assets/discover-france-4.jpeg';
import yusurf from '../assets/yusurf.jpg';

export default function Home() {
  return (
    <div className="flex flex-col min-h-screen pt-16">
      
      {/* Hero Section */}
      <motion.div 
        initial={{ opacity: 0 }} 
        animate={{ opacity: 1 }} 
        transition={{ duration: 0.8 }}
        className="relative bg-blue-950 text-white overflow-hidden"
      >
        {/* Background Overlay */}
        <div className="absolute inset-0 z-0 pointer-events-none overflow-hidden">
          <motion.img 
            initial={{ scale: 1.1 }}
            animate={{ scale: 1 }}
            transition={{ duration: 6, ease: "easeOut" }}
            src="https://images.unsplash.com/photo-1499856871958-5b9627545d1a?q=80&w=2070&auto=format&fit=crop" 
            alt="Paris background" 
            className="w-full h-full object-cover opacity-60 mix-blend-overlay"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-blue-950 via-blue-900/80 to-blue-900/40"></div>
          
          {/* Live Animated Orbs and French Elements */}
          <motion.div 
            animate={{ y: [0, -40, 0], x: [0, 20, 0], opacity: [0.3, 0.6, 0.3] }} 
            transition={{ duration: 10, repeat: Infinity, ease: 'easeInOut' }}
            className="absolute top-10 left-10 w-[500px] h-[500px] bg-blue-500/30 rounded-full blur-[120px] mix-blend-screen"
          />
          <motion.div 
            animate={{ y: [0, 30, 0], x: [0, -30, 0], opacity: [0.2, 0.5, 0.2] }} 
            transition={{ duration: 14, repeat: Infinity, ease: 'easeInOut' }}
            className="absolute bottom-10 right-10 w-[600px] h-[600px] bg-cyan-400/20 rounded-full blur-[140px] mix-blend-screen"
          />
          {/* Floating French Elements */}
          <motion.div
            animate={{ y: [0, -20, 0], rotate: [0, 5, -5, 0] }}
            transition={{ duration: 6, repeat: Infinity, ease: 'easeInOut' }}
            className="absolute top-32 right-[15%] text-7xl opacity-40 select-none grayscale hidden md:block"
          >
            🗼
          </motion.div>
          <motion.div
            animate={{ y: [0, 30, 0], rotate: [0, -10, 10, 0], opacity: [0.2, 0.5, 0.2] }}
            transition={{ duration: 8, repeat: Infinity, ease: 'easeInOut', delay: 2 }}
            className="absolute bottom-40 left-[10%] text-6xl select-none grayscale hidden md:block"
          >
            🥐
          </motion.div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-24 sm:py-28 lg:py-36 flex flex-col items-start text-left">
          <motion.span 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
            className="text-blue-200 font-medium tracking-[0.2em] uppercase text-xs sm:text-sm mb-6 block"
          >
            TS Language School • <strong>24/7 Support</strong>
          </motion.span>
          <motion.h1 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.4 }}
            className="text-4xl sm:text-5xl lg:text-7xl font-semibold leading-tight mb-8 max-w-4xl tracking-tight"
          >
            DISCOVER THE ELEGANCE OF <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-white font-light italic">LEARNING LANGUAGES</span>
          </motion.h1>
          <motion.p 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.6 }}
            className="text-blue-100 text-lg sm:text-xl md:text-2xl mb-12 leading-relaxed max-w-2xl font-light"
          >
            Learn Online or In-Person with Expert & Passionate Instructors. Speak confidently and discover the world from a new perspective.
          </motion.p>
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.8 }}
            className="flex flex-col sm:flex-row gap-4 w-full sm:w-auto"
          >
            <Link to="/contact" className="px-8 sm:px-10 py-4 bg-white text-blue-900 font-medium rounded-sm hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-900/20 transition-all duration-300 text-base sm:text-lg text-center w-full sm:w-auto">
              Start Your Language Journey Today
            </Link>
            <Link to="/courses" className="px-8 sm:px-10 py-4 border border-white/50 backdrop-blur-sm text-white font-medium rounded-sm hover:bg-white hover:text-blue-900 hover:border-white hover:-translate-y-1 transition-all duration-300 text-base sm:text-lg text-center shadow-lg w-full sm:w-auto">
              View Our Offerings
            </Link>
          </motion.div>
        </div>
      </motion.div>


      {/* Let's Talk About Numbers */}
      <motion.section 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-100px" }}
        transition={{ duration: 0.7 }}
        className="py-24 relative bg-blue-900 border-b border-slate-200 overflow-hidden"
      >
        <div className="absolute inset-0 z-0 pointer-events-none overflow-hidden">
          <motion.img 
            animate={{ scale: [1, 1.05, 1] }}
            transition={{ duration: 20, repeat: Infinity, ease: "linear" }}
            src="https://images.unsplash.com/photo-1502602898657-3e907a5ea82c?q=80&w=2070&auto=format&fit=crop" 
            alt="Paris at sunset" 
            className="w-full h-full object-cover opacity-25 mix-blend-overlay fixed"
          />
        </div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h2 className="text-blue-300 font-medium tracking-widest uppercase text-sm mb-2">Our Reach</h2>
          <h3 className="text-3xl md:text-5xl font-semibold text-white mb-16 tracking-tight">Let's Talk About Numbers</h3>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-5xl mx-auto">
            <div className="p-8 bg-blue-950/40 backdrop-blur-md rounded-sm border border-blue-800/50 hover:-translate-y-1 hover:bg-blue-900/60 transition-all duration-300 shadow-xl group">
              <div className="text-4xl md:text-6xl font-bold text-white mb-2 group-hover:scale-110 transition-transform origin-bottom font-light">6+</div>
              <div className="text-sm font-medium text-blue-300 uppercase tracking-wider">Years of Excellence</div>
            </div>
            <div className="p-8 bg-blue-950/40 backdrop-blur-md rounded-sm border border-blue-800/50 hover:-translate-y-1 hover:bg-blue-900/60 transition-all duration-300 shadow-xl group">
              <div className="text-4xl md:text-6xl font-bold text-white mb-2 group-hover:scale-110 transition-transform origin-bottom font-light">3K+</div>
              <div className="text-sm font-medium text-blue-300 uppercase tracking-wider">Happy Learners</div>
            </div>
            <div className="p-8 bg-blue-950/40 backdrop-blur-md rounded-sm border border-blue-800/50 hover:-translate-y-1 hover:bg-blue-900/60 transition-all duration-300 shadow-xl group">
              <div className="text-4xl md:text-6xl font-bold text-white mb-2 group-hover:scale-110 transition-transform origin-bottom font-light">20+</div>
              <div className="text-sm font-medium text-blue-300 uppercase tracking-wider">Expert Instructors</div>
            </div>
            <div className="p-8 bg-blue-950/40 backdrop-blur-md rounded-sm border border-blue-800/50 hover:-translate-y-1 hover:bg-blue-900/60 transition-all duration-300 shadow-xl group">
              <div className="text-4xl md:text-6xl font-bold text-white mb-2 group-hover:scale-110 transition-transform origin-bottom font-light">98%</div>
              <div className="text-sm font-medium text-blue-300 uppercase tracking-wider">Exam Success Rate</div>
            </div>
          </div>
        </div>
      </motion.section>

      {/* Specialities / Age Group & App */}
      <motion.section 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-50px" }}
        transition={{ duration: 0.7 }}
        className="py-24 bg-white border-b border-slate-200"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-16">
            <h2 className="text-blue-600 font-medium tracking-widest uppercase text-sm mb-2">Our Flex</h2>
            <h3 className="text-3xl md:text-5xl font-semibold text-slate-900 mb-6 tracking-tight">Learning Built for Everyone</h3>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
            <div className="bg-slate-50 p-10 lg:p-12 rounded-sm border border-slate-100 hover:shadow-lg transition-shadow">
              <h4 className="text-2xl font-semibold text-slate-900 mb-6 flex items-center gap-4">
                <div className="p-3 bg-blue-100 text-blue-600 rounded flex items-center justify-center">
                  <Users className="h-8 w-8" />
                </div>
                For Every Age-Group
              </h4>
              <p className="text-slate-600 mb-8 leading-relaxed text-lg font-light">
                Whether you are introducing a child to a new language, supporting a teenager through exams, or upgrading your professional skills as an adult, we have tailored curriculums designed exactly for your cognitive and developmental needs.
              </p>
              <ul className="space-y-4">
                <li className="flex items-center gap-3"><CheckCircle2 className="h-5 w-5 text-blue-600" /><span className="font-medium text-slate-800">Kids Programs (Ages 6-12)</span></li>
                <li className="flex items-center gap-3"><CheckCircle2 className="h-5 w-5 text-blue-600" /><span className="font-medium text-slate-800">Teens Prep (Ages 13-17)</span></li>
                <li className="flex items-center gap-3"><CheckCircle2 className="h-5 w-5 text-blue-600" /><span className="font-medium text-slate-800">Adults & Professionals (18+)</span></li>
              </ul>
            </div>

            <div className="bg-slate-50 p-10 lg:p-12 rounded-sm border border-slate-100 hover:shadow-lg transition-shadow">
              <h4 className="text-2xl font-semibold text-slate-900 mb-6 flex items-center gap-4">
                <div className="p-3 bg-blue-100 text-blue-600 rounded flex items-center justify-center">
                  <GraduationCap className="h-8 w-8" />
                </div>
                Certification & Careers
              </h4>
              <p className="text-slate-600 mb-8 leading-relaxed text-lg font-light">
                In today's globalized economy, bilingualism is your ultimate competitive edge. We don't just teach you to speak; we prepare you to officially certify your proficiency.
              </p>
              <ul className="space-y-4">
                <li className="flex items-center gap-3"><CheckCircle2 className="h-5 w-5 text-blue-600" /><span className="font-medium text-slate-800">Immigration Pathways (Canada)</span></li>
                <li className="flex items-center gap-3"><CheckCircle2 className="h-5 w-5 text-blue-600" /><span className="font-medium text-slate-800">Globally Recognised Certificates</span></li>
                <li className="flex items-center gap-3"><CheckCircle2 className="h-5 w-5 text-blue-600" /><span className="font-medium text-slate-800">Enhanced Career Opportunities</span></li>
              </ul>
            </div>
          </div>



        </div>
      </motion.section>

      {/* Team / Expert Instructors */}
      <motion.section 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-50px" }}
        transition={{ duration: 0.7 }}
        className="py-24 bg-white border-b border-slate-200 relative overflow-hidden" id="team"
      >
        <div className="absolute inset-0 z-0 pointer-events-none">
          <img src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=2070&auto=format&fit=crop" className="w-full h-full object-cover opacity-[0.04]" alt="Background pattern" />
        </div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center max-w-3xl mx-auto mb-16">
            <h2 className="text-blue-600 font-medium tracking-widest uppercase text-sm mb-2">Our Team</h2>
            <h3 className="text-3xl md:text-4xl font-semibold text-slate-900 mb-6 tracking-tight">Learn With Expert & Passionate Instructors</h3>
          </div>
          
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              { name: 'Arun', role: 'Head of French Department', img: arun },
              { name: 'Akansha Arora', role: 'TCF/TEF Examiner & Tutor', img: akansha },
              { name: 'Yusuf', role: 'TCF/TEF Examiner & Tutor', img: yusurf },
              { name: 'Cyrille Helena', role: 'DALF Advanced Instructor', img: cyrille }
            ].map((member, idx) => (
              <div key={idx} className="bg-white rounded-sm border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div className="h-64 overflow-hidden relative">
                  <img src={member.img} alt={member.name} className="w-full h-full object-cover grayscale opacity-90 group-hover:grayscale-0 group-hover:scale-105 transition-all duration-700" />
                  <div className="absolute inset-0 bg-blue-900/20 group-hover:bg-transparent transition-colors duration-500"></div>
                </div>
                <div className="p-6 text-center border-t-4 border-transparent group-hover:border-blue-600 transition-colors">
                  <h4 className="text-xl font-semibold text-slate-900 mb-1">{member.name}</h4>
                  <p className="text-blue-600 font-medium text-sm tracking-wide">{member.role}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </motion.section>

      {/* Google Reviews */}
      <motion.section 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-50px" }}
        transition={{ duration: 0.7 }}
        className="py-24 bg-slate-50 border-b border-slate-200 overflow-hidden relative"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="text-center max-w-4xl mx-auto mb-16">
            <h2 className="text-blue-600 font-medium tracking-widest uppercase text-sm mb-2">Authentic Feedback</h2>
            <h3 className="text-3xl md:text-5xl font-semibold text-slate-900 mb-6 tracking-tight uppercase">Read What TS Students Are Saying</h3>
          </div>

          <motion.div 
            initial={{ opacity: 0, x: 20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="flex overflow-x-auto pb-8 gap-6 snap-x hide-scrollbars no-scrollbar"
          >
            {[
              { name: "Swedha Sri", time: "a week ago", text: "Highly recommended for learning French and clearing TCF exam." },
              { name: "Shri Mathi M", time: "a week ago", text: "The classes are interactive and easy to understand. Excellent for French beginners and TCF preparation." },
              { name: "Dhipak Sankar", time: "a week ago", text: "Strong focus on speaking and writing skills. Perfect for TCF exam success" },
              { name: "Fahima", time: "a week ago", text: "TS Language School stands out for its high-quality French training and personalized guidance. The TCF exam coaching is systematic and focused, helping students achieve their immigration goals efficiently." },
              { name: "Sithananthan S", time: "a week ago", text: "Strong focus on communication and exam preparation" },
              { name: "Shailendhirah", time: "2 weeks ago", text: "Trainers are experienced and supportive. Good environment for French learning and Canada PR preparation." },
              { name: "SRI RAM S", time: "2 weeks ago", text: "Highly professional training with real-time practice. Best for TCF and Canada PR aspirants." },
              { name: "Yuvaraj S", time: "2 weeks ago", text: "TS Language School provides outstanding support for French learning and TCF exam success. Their commitment to quality education makes them a reliable partner for achieving Canada PR goals." },
              { name: "Diya R", time: "2 weeks ago", text: "Very supportive trainers and structured learning. Perfect place for French learning and TCF exam." },
              { name: "PURUSHOTH A", time: "2 weeks ago", text: "TS Language School helped me improve my French skills confidently. Strong support for TCF Exam" },
              { name: "MAHESHWARAN", time: "a week ago", text: "The classes are interactive and easy to understand. Excellent for French beginners and TCF preparation." },
              { name: "Haritha Ragu", time: "a week ago", text: "Best place for learning French with practical sessions. Helpful for Canada PR process" },
              { name: "Deva Sri", time: "a week ago", text: "Excellent support for Canada PR through French." },
              { name: "Harita Versni", time: "a week ago", text: "Great environment for learning French confidently." },
              { name: "Shivani Dharsika", time: "a week ago", text: "TS Language School provides a comfortable environment for learning languages. The staff are supportive and teach in a very simple way. I gained better confidence in speaking after joining here." }
            ].map((review, idx) => (
              <motion.div 
                key={idx} 
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.1 }}
                className="min-w-[300px] md:min-w-[350px] bg-white flex-shrink-0 snap-center rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow"
              >
                {/* Header */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className={`w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg ${
                      review.name === "Swedha Sri" ? "bg-slate-800" :
                      review.name === "Shri Mathi M" ? "bg-amber-800" :
                      review.name === "Dhipak Sankar" ? "bg-green-600" :
                      review.name === "Fahima" ? "bg-pink-400" :
                      review.name === "Shailendhirah" ? "bg-red-600" :
                      review.name === "SRI RAM S" ? "bg-black" :
                      review.name === "Yuvaraj S" ? "bg-slate-500" :
                      review.name === "Diya R" ? "bg-pink-500" :
                      review.name === "PURUSHOTH A" ? "bg-amber-900" :
                      review.name === "MAHESHWARAN" ? "bg-slate-700" :
                      review.name === "Haritha Ragu" ? "bg-blue-500" :
                      review.name === "Deva Sri" ? "bg-blue-600" :
                      review.name === "Harita Versni" ? "bg-purple-700" :
                      review.name === "Shivani Dharsika" ? "bg-yellow-600" :
                      "bg-slate-400"
                    }`}>
                      {review.name.charAt(0)}
                    </div>
                    <div>
                      <div className="font-semibold text-sm text-slate-900">{review.name}</div>
                      <div className="text-xs text-slate-500">{review.time}</div>
                    </div>
                  </div>
                  {/* Google Icon SVG */}
                  <div className="w-6 h-6 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" className="w-full h-full">
                      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                  </div>
                </div>
                {/* Stars */}
                <div className="flex gap-1">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-yellow-400 text-yellow-400" />
                  ))}
                </div>
                {/* Body */}
                <p className="text-slate-700 text-sm leading-relaxed">
                  {review.text}
                </p>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </motion.section>

      {/* French Quiz Section */}
      <FrenchQuiz />

      {/* France Gallery Section */}
      <motion.section 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-100px" }}
        transition={{ duration: 0.7 }}
        className="py-24 bg-white border-y border-slate-200"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
             <h2 className="text-blue-600 font-semibold tracking-wide uppercase text-sm mb-2">IMMERSION</h2>
             <h3 className="text-4xl md:text-5xl font-semibold tracking-tight text-slate-900 mb-6">Discover France</h3>
             <p className="text-xl text-slate-600 max-w-2xl mx-auto">Immerse yourself in French culture, art, architecture, and lifestyle.</p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="col-span-1 md:col-span-2 h-64 md:h-80 relative rounded-sm overflow-hidden group">
                <img src={discoverFrance1} alt="Discover France" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                <h4 className="text-white text-2xl font-bold tracking-tight">Paris - Tour Eiffel</h4>
              </div>
            </div>
            <div className="col-span-1 h-64 md:h-80 relative rounded-sm overflow-hidden group">
                <img src={discoverFrance2} alt="Discover France" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                 <h4 className="text-white text-xl font-bold tracking-tight">Musée du Louvre</h4>
              </div>
            </div>
            <div className="col-span-1 h-64 md:h-80 relative rounded-sm overflow-hidden group">
                <img src={discoverFrance3} alt="Discover France" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                 <h4 className="text-white text-xl font-bold tracking-tight">Côte d'Azur</h4>
              </div>
            </div>
            <div className="col-span-1 md:col-span-2 h-64 md:h-80 relative rounded-sm overflow-hidden group">
                <img src={discoverFrance4} alt="Discover France" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
               <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                 <h4 className="text-white text-2xl font-bold tracking-tight">Alpes Françaises</h4>
              </div>
            </div>
          </div>
        </div>
      </motion.section>

      {/* Blogs & Resources */}
      <motion.section 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-50px" }}
        transition={{ duration: 0.7 }}
        className="py-24 bg-slate-50 border-b border-slate-200" id="blog"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="max-w-2xl mb-12">
              <h2 className="text-blue-600 font-medium tracking-widest uppercase text-sm mb-2">Knowledge Base</h2>
              <h3 className="text-3xl md:text-5xl font-semibold text-slate-900 tracking-tight">French Resources</h3>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {[
                {
                  tag: 'REFERENCE',
                  title: 'French Language - an overview | ScienceDirect Topics',
                  desc: 'A broad topic overview covering French language background, usage, and related academic references.',
                  href: 'https://www.sciencedirect.com/topics/social-sciences/french-language',
                  image: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop'
                },
                {
                  tag: 'MAGAZINE',
                  title: 'French | Language Magazine',
                  desc: 'A curated French language resource page with learning context, study-abroad references, and language articles.',
                  href: 'https://languagemagazine.com/french/',
                  image: 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?q=80&w=800&auto=format&fit=crop'
                },
                {
                  tag: 'JOURNAL',
                  title: 'Journal articles: French language - Foreign elements - English | Grafiati',
                  desc: 'A research-oriented listing of journal articles and scholarly references related to French and English language influence.',
                  href: 'https://www.grafiati.com/en/literature-selections/french-language-foreign-elements-english/journal/',
                  image: 'https://images.unsplash.com/photo-1513258496099-48168024aec0?q=80&w=800&auto=format&fit=crop'
                }
              ].map((resource) => (
                <a
                  key={resource.href}
                  href={resource.href}
                  target="_blank"
                  rel="noreferrer"
                  className="group block"
                >
                  <div className="h-72 overflow-hidden rounded-sm mb-6 relative">
                    <img src={resource.image} alt={resource.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                    <div className="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-medium text-blue-800 shadow-md tracking-wider">{resource.tag}</div>
                  </div>
                  <h4 className="text-2xl font-semibold text-slate-900 mb-4 group-hover:text-blue-600 transition-colors leading-snug">{resource.title}</h4>
                  <p className="text-slate-600 mb-4 line-clamp-3 text-lg leading-relaxed font-light">{resource.desc}</p>
                  <span className="text-blue-600 font-medium flex items-center group-hover:underline underline-offset-4">Open Resource <ChevronRight className="h-5 w-5 ml-1" /></span>
                </a>
              ))}
            </div>
          </div>
        </motion.section>


    </div>
  );
}