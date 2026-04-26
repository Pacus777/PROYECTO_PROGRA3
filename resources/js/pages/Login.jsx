import React, { useState } from 'react';
import axios from 'axios';
import { Link, useNavigate } from 'react-router-dom';

export default function Login() {
    const navigate = useNavigate();
    const [correo_usu, setCorreo] = useState('');
    const [password_usu, setPassword] = useState('');
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    async function handleSubmit(e) {
        e.preventDefault();
        setError(null);
        setLoading(true);
        try {
            const { data } = await axios.post('/auth/login', { correo_usu, password_usu });
            const token = data?.data?.token;
            if (token) {
                localStorage.setItem('auth_token', token);
            }

            navigate('/dashboard');
        } catch (err) {
            const msg = err.response?.data?.message
                ?? err.response?.data?.errors?.correo_usu?.[0]
                ?? 'No se pudo iniciar sesión.';
            setError(msg);
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="flex min-h-screen items-center justify-center px-4">
            <div className="w-full max-w-md rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 className="text-xl font-semibold text-slate-800">Iniciar sesión</h1>
                <p className="mt-1 text-sm text-slate-500">Sistema de admisión escolar</p>

                {error && (
                    <div className="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">
                        {error}
                    </div>
                )}

                <form className="mt-6 space-y-4" onSubmit={handleSubmit}>
                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="correo">Correo</label>
                        <input
                            id="correo"
                            type="email"
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            value={correo_usu}
                            onChange={(ev) => setCorreo(ev.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="clave">Contraseña</label>
                        <input
                            id="clave"
                            type="password"
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            value={password_usu}
                            onChange={(ev) => setPassword(ev.target.value)}
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700 disabled:opacity-60"
                    >
                        {loading ? 'Entrando…' : 'Entrar'}
                    </button>
                </form>

                <p className="mt-6 text-center text-sm text-slate-500">
                    ¿Sin cuenta?{' '}
                    <Link className="font-medium text-indigo-600 hover:text-indigo-500" to="/registro">
                        Crear cuenta tutor
                    </Link>
                </p>
            </div>
        </div>
    );
}
