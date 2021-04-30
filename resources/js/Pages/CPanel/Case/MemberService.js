import axios from 'axios';

export default class MemberService {
    getMembers() {
        return axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/members').then(res => res.data.data)
                })
    }
}