import $ from 'jquery';
import Mask from 'mod_contractactivity/mask'
import ValidateFiles from 'mod_contractactivity/validate_files'
import ViaCep from 'mod_contractactivity/viacep'


const mask = async () => {
    return await Mask.init();
}
const validateFiles = async () => {
    return await ValidateFiles.init();
}
const viaCep = async () => {
    return await ViaCep.init();
}

export default {
    mask,
    validateFiles,
    viaCep,
}